<?php

class ReportAction extends Action {
	// 显示概览
	public function summary() {
		$test = D('Common', 'test')->r($_GET['test_id']);
		$test = A('Test')->format($test);
		$cd = D('Common', 'candidate')->r($_GET['cd_id']);
		$cd = A('Candidate')->format($cd);
		$rank = D('Common', 'candidate')->rCountBySql(array('test_id' => $cd['test_id'], 'score' => array('gt', $cd['score'])));
		$rank += D('Common', 'candidate')->rCountBySql(array('test_id' => $cd['test_id'], 'score' => $cd['score'], 'tot_time_int' => array('lt', $cd['tot_time_int'])));
		$rank++;
		
		$this->assign('test', $test);
		$this->assign('cd', $cd);
		$this->assign('rank', $rank);
		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = $cd['name'];
		if (!empty($page['pageTitle']))
			$page['pageTitle'] .= ' - ';
		$page['pageTitle'] .= $cd['email'];
		$page['item1'] = 'report';
		$page['item2'] = '';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}

	// 显示详情
	public function detail() {
		$cd = D('Common', 'candidate')->r($_GET['cd_id']);
		$cd = A('Candidate')->format($cd);
		
		// 获取题目ID列表
		$tmpList = D('Common', 'test_question')->rList(array('test_id' => $_GET['test_id'], 'page' => 'all'), array('order' => array('field' => 'rank', 'type' => 'ASC')));
		$quesIdList = array();		
		foreach($tmpList['data'] as $item) {
			$quesIdList[] = $item['question_id'];
		}
		
		// 获取题目列表
		$tmpQuesList = D('Common', 'question')->rList(array('id' => $quesIdList, 'page' => 'all'), array());
		$tmpQuesList = $tmpQuesList['data'];
		foreach($tmpQuesList as $key => $item) {
			$tmpQuesList[$key] = A('Question')->format($item);
		}
		// 获取四种类型题目的详细信息
		$tmpList = array(1 => 'single', 2 => 'multi', 3 => 'qa', 4 => 'program');
		$tmpList2 = array();
		foreach($tmpQuesList as $item) {
			$tmpList2[$item['type_id']][] = $item['id'];
		}
		$tmpList3 = array();
		foreach($tmpList as $key => $item) {
			$tmpList3[] = D('Common', 'q_' . $item)->rList(array('id' => $tmpList2[$key], 'page' => 'all'), array());
			$cnt = count($tmpList3) - 1;
			$tmpList3[$cnt] = $tmpList3[$cnt]['data'];
			
			foreach($tmpList3[$cnt] as $key2 => $item2) {
				if ($key == 1)
					$tmpList3[$cnt][$key2] = A('QBase')->formatSingle($item2);
				if ($key == 2)
					$tmpList3[$cnt][$key2] = A('QBase')->formatMulti($item2);
				if ($key == 3)
					$tmpList3[$cnt][$key2] = A('QBase')->formatQA($item2);
				if ($key == 4)
					$tmpList3[$cnt][$key2] = A('QProgram')->format($item2);
			}
		}
		// 合并到总题目表中
		foreach($tmpQuesList as $key => $item) {
			foreach($tmpList3 as $item2) {
				foreach($item2 as $item3) {
					if ($item3['id'] == $item['id']) {
						$tmpQuesList[$key]['detail'] = $item3;
					}
				}
			}
		}

		// 获取答案
		$ansList = D('Common', 'answer')->rList(array('candidate_id' => $_GET['cd_id'], 'page' => 'all'), array('order' => array('field' => 'candidate_id', 'type' => 'ASC')));
		$ansList = $ansList['data'];
		// 格式化答案
		foreach($ansList as $key => $item) {
			$ansList[$key]['tot_time'] = '';
			if ($item['tot_time_int'] >= 60)
				$ansList[$key]['tot_time'] .= (int)($item['tot_time_int'] / 60) . '分';
			$ansList[$key]['tot_time'] .= ($item['tot_time_int'] % 60) . '秒';
		}
		// 获取编程题答案的详细信息
		$tmpList3 = D('Common', 'a_program')->rList(array('candidate_id' => $_GET['cd_id'], 'question_id' => $tmpList2[4], 'page' => 'all'), array('order' => array('field' => 'candidate_id', 'type' => 'ASC')));
		$tmpList3 = $tmpList3['data'];
		// 合并到答案里
		foreach($ansList as $key => $item) {
			foreach($tmpList3 as $item2) {
				if ($item['question_id'] == $item2['question_id']) {
					// 格式化程序
					$item2 = A('QProgram')->formatAns($item2);
					$ansList[$key]['detail'] = $item2;
				}
			}
		}		
		// 合并到总题目表里
		foreach($tmpQuesList as $key => $item) {
			foreach($ansList as $item2) {
				if ($item['id'] == $item2['question_id']) {
					$tmpQuesList[$key]['ans'] = $item2;
				}
			}
		}
		
		// 根据题目顺排序
		$quesList = array();
		foreach($quesIdList as $item) {
			foreach($tmpQuesList as $item2) {
				if ($item == $item2['id']) {
					$quesList[] = $item2;
				}
			}
		}

		$this->assign('get', $_GET);
		$this->assign('quesList', $quesList);
		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = $cd['name'];
		if (!empty($page['pageTitle']))
			$page['pageTitle'] .= ' - ';
		$page['pageTitle'] .= $cd['email'];
		$page['item1'] = 'report';
		$page['item2'] = '';
		$page['content'] = $this->fetch();
		//echo $page['content'];
		$this->ajaxReturn($page);
	}
	
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
		$this->assign('test', D('Common', 'test')->r($_GET['test_id']));
	
		$page['pageTitle'] = '测评中';
		$page['item1'] = 'report';
		$page['item2'] = 'running';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 已完成
	public function completed() {
		// 获取数据
		$filter = array(
			'status_id' => 3,
			'page' => $_GET['page'],
			'test_id' => $_GET['test_id'],
		);
		$const = array();
		$cdList = $this->getList($filter, $const);

		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = '已完成';
		$page['item1'] = 'report';
		$page['item2'] = 'completed';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 已通过
	public function passed() {
		// 获取数据
		$filter = array(
			'status_id' => 4,
			'page' => $_GET['page'],
			'test_id' => $_GET['test_id'],
		);
		$const = array();
		$cdList = $this->getList($filter, $const);

		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = '已通过';
		$page['item1'] = 'report';
		$page['item2'] = 'passed';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 已淘汰
	public function failed() {
		// 获取数据
		$filter = array(
			'status_id' => 5,
			'page' => $_GET['page'],
			'test_id' => $_GET['test_id'],
		);
		$const = array();
		$cdList = $this->getList($filter, $const);

		$this->assign('reportCount', $this->getCount($_GET['test_id']));
	
		$page['pageTitle'] = '已淘汰';
		$page['item1'] = 'report';
		$page['item2'] = 'failed';
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
		
		$error = A('Common')->isCorrect(
			array(
				'email' => $_POST['email'], 
				'title' => $_POST['title'],
			), 
			array(
				array('email', 'empty'),
				array('email', 'length',  array(1, 800)),
				array('title', 'empty'),
				array('title', 'length',  array(1, 30)),
			)
		);
		
		if (!empty($error)) {
			$ret['status'] = 'fail';
			$ret['error'] = $error;
			$this->ajaxReturn($ret);
		}
		
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
		$cdList = D('Common', 'candidate')->rList(array('test_id' => $test['id'], 'email' => $email), array());
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
			$data[$cnt]['invite_time_int'] = getTime();
			$data[$cnt]['status_id'] = 1;
			$data[$cnt]['judged'] = 1;
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
			$mailCont = str_replace('<{link}>', C('ROOT_URL') . '/attend/login/id/' . $test['id'], $mailCont);
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

	// 通过
	public function pass() {
		$testId = $_GET['test_id'];
		$idList = split('\|', $_GET['idList']);
		$this->deleteCount($testId, $idList);
		$ret = D('Common', 'candidate')->uBySql(array('status_id' => 4), array('id' => array('in', $idList)));
		D('Common', 'test')->incField('id', $testId, 'count_passed', (count($idList) - 1));
		$this->ajaxReturn(array('backward' => '1'));
	}
	
	// 淘汰
	public function fail() {
		$testId = $_GET['test_id'];
		$idList = split('\|', $_GET['idList']);
		$this->deleteCount($testId, $idList);
		$ret = D('Common', 'candidate')->uBySql(array('status_id' => 5), array('id' => array('in', $idList)));
		D('Common', 'test')->incField('id', $testId, 'count_failed', (count($idList) - 1));
		$this->ajaxReturn(array('backward' => '1'));
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
	
	// 更改分数
	public function changeScore() {
		$ans = D('Common', 'answer')->rBySql(array('candidate_id' => $_GET['cd_id'], 'question_id' => $_GET['question_id']));
		$data['candidate_id'] = $_GET['cd_id'];
		$data['question_id'] = $_GET['question_id'];
		$data['score'] = $_GET['score'];
		D('Common', 'answer')->u($data);
		
		D('Common', 'candidate')->incField('id', $_GET['cd_id'], 'score', ($_GET['score'] - $ans['score']));
	}
	
	// 格式化
	public function format($cd) {
		return A('Candidate')->format($cd);
	}
}