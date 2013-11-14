<?php

class ReportAction extends Action {
	// 获取用户列表
	public function getList($filter, $const) {
		// 获取数据
		$ret = D('Common', 'candidate')->rList($filter, $const);
		$cdList = $ret['data'];
		foreach($cdList as $key => $test) {
			$cdList[$key] = $this->format($test);
		}

		// 分页信息
		$pager['count'] = $ret['count'];
		$pager['cntPage'] = $_GET['page'];
		if (empty($pager['cntPage'])) $pager['cntPage'] = 1;
		$pager['totPage'] =  ceil($pager['count'] / 10);

		$this->assign('pager', $pager);
		$this->assign('cdList', $cdList);
	}

	// 已邀请
	public function invited() {
		// 获取数据
		$filter = array(
			'status_id' => 1,
			'page' => $_GET['page'],
			'test_id' => $_GET['test_id'],
		);
		$const = array();
		$cdList = $this->getList($filter, $const);

		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = '已邀请';
		$page['item1'] = 'report';
		$page['item2'] = 'invited';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}

	// 测评中
	public function running() {
		// 获取数据
		$filter = array(
			'status_id' => 2,
			'page' => $_GET['page'],
			'test_id' => $_GET['test_id'],
		);
		$const = array();
		$cdList = $this->getList($filter, $const);

		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = '测评中';
		$page['item1'] = 'report';
		$page['item2'] = 'running';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 邀请面试
	public function invite() {
		$test = D('Common', 'test')->r($_GET['test_id']);
		if (!empty($_GET['idList'])) {
			$idList = split('\|', $_GET['idList']);
			$filter['id'] = $idList;
			$cdList = D('Common', 'candidate')->rList($filter, array());
			$emailList = '';
			foreach($cdList['data'] as $item) {
				$emailList .= $item['email'] . "\n";
			}
			$this->assign('emailList', $emailList);
		}
		
		$this->assign('test', $test);
		$this->assign('reportCount', $this->getCount($_GET['test_id']));
		
		$page['pageTitle'] = '邀请候选人';
		$page['item1'] = 'report';
		$page['item2'] = '';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 邀请面试处理
	public function inviteDo() {
		$user = D('Common', 'user')->r($_SESSION['id']);
		$test = D('Common', 'test')->r($_POST['test_id']);
		$test = A('Test')->format($test);
		
		if (!empty($_POST['content']))
			$this->assign('hasContent', true);
		if (empty($test['start_datetime_int']) && empty($test['end_datetime_int']))
			$this->assign('timeMode', 0);
		if (!empty($test['start_datetime_int']) && empty($test['end_datetime_int']))
			$this->assign('timeMode', 1);
		if (empty($test['start_datetime_int']) && !empty($test['end_datetime_int']))
			$this->assign('timeMode', 2);
		if (!empty($test['start_datetime_int']) && !empty($test['end_datetime_int']))
			$this->assign('timeMode', 3);
		
		$email = $_POST['email'];
		$email = str_replace("\r", '', $email);
		$email = split("\n", $email);
		
		// 删除已存在的用户
		$cdList = D('Common', 'candidate')->rList(array('email' => $email), array());
		$idList = array();
		foreach($cdList['data'] as $cd) {
			$idList[] = $cd['id'];
		}
		$this->delete($test['id'], $idList, true);
		
		// 插入新用户
		$data = array();
		foreach($email as $item) {
			if (empty($item)) continue;
			// 如果已经存在
			$flag = false;
			foreach($data as $item2) {
				if ($item == $item2['email']) {
					$flag  = true;
					break;
				}
			}
			if ($flag) continue;
			// 生成随机密码
			$password = randomChar();
			// 插入数据库
			$data[] = array();
			$cnt = count($data) - 1;
			$data[$cnt]['user_id'] = $_SESSION['id'];
			$data[$cnt]['test_id'] = $test['id'];
			$data[$cnt]['email'] = $item;
			$data[$cnt]['password'] = $password;
			$data[$cnt]['invite_time_int'] = getTime();;
			$data[$cnt]['status_id'] = 1;
		}
		D('Common', 'candidate')->cArr($data);
		D('Common', 'test')->incField('id', $test['id'], 'count_invited', count($data));

		// 发送邮件
		set_time_limit(3600);
		foreach($data as $item) {
			$mailCont = $this->fetch('Report:inviteMail');
			$mailCont = str_replace('<{title}>', $_POST['title'], $mailCont);
			$mailCont = str_replace('<{content}>', nl2br($_POST['content']), $mailCont);
			$mailCont = str_replace('<{startTime}>', $test['start_date'] . ' ' . $test['start_time'], $mailCont);
			$mailCont = str_replace('<{endTime}>', $test['end_date'] . ' ' . $test['end_time'], $mailCont);
			$mailCont = str_replace('<{duration}>', $test['duration'], $mailCont);
			$mailCont = str_replace('<{link}>', C('ROOT_URL') . '/attend', $mailCont);
			$mailCont = str_replace('<{email}>', $item['email'], $mailCont);
			$mailCont = str_replace('<{password}>', $item['password'], $mailCont);
			$mailCont = str_replace('<{companyName}>', $user['company_name'], $mailCont);
			sendEmail($item['email'], '[测评邀请] ' . $_POST['title'], $mailCont);
		}
		
		if (empty($data))
			$ret['status'] = 'fail';
		else {
			$ret['status'] = 'success';
			$ret['linkUrl'] = '/report/invited';
		}
		$this->ajaxReturn($ret);
	}

	// 删除候选人
	public function delete($testId = 0, $cdIdList = 0, $noReturn = 0) {
		if (empty($testId))
			$testId = $_GET['test_id'];
		if (empty($cdIdList))
			$idList = split('\|', $_GET['idList']);
		else
			$idList = $cdIdList;
		$this->deleteCount($testId, $idList);
		D('Common', 'candidate')->dList($idList);
		if (!$noReturn) $this->ajaxReturn(array('backward' => '1'));
	}
	
	// 删除之前的统计数据
	public function deleteCount($testId, $idList) {		
		$cdList = D('Common', 'candidate')->rList(array('id' => $idList), array());
		$decList = array();
		foreach($cdList['data'] as $cd) {
			if ($cd['status_id'] == 1)
				$decList['count_invited']++;
			if ($cd['status_id'] == 2)
				$decList['count_running']++;
			if ($cd['status_id'] == 3)
				$decList['count_completed']++;
			if ($cd['status_id'] == 4)
				$decList['count_passed']++;
			if ($cd['status_id'] == 5)
				$decList['count_failed']++;
		}
		foreach($decList as $key => $item) {
			$decList[$key] = array('exp', '`' . $key . '` - ' . $item);
		}
		$decList['id'] = $testId;
		D('Common', 'test')->u($decList);
	}
	
	// 获取每个类别的次数
	public function getCount($id) {
		$test = D('Common', 'test')->rFieldBySql('`count_invited`, `count_running`, `count_completed`, `count_passed`, `count_failed`', array('id' => $id));
		return $test;
	}
	
	// 格式化
	public function format($cd) {
		$cd['invite_time'] = intToTime($cd['invite_time_int'], 'Y-m-d H:i');
		$cd['start_time'] = intToTime($cd['start_time_int'], 'Y-m-d H:i');
		$cd['end_time'] = intToTime($cd['end_time_int'], 'Y-m-d H:i');
		return $cd;
	}
}