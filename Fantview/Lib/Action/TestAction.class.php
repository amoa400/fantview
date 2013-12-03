<?php

// 已加权限

class TestAction extends Action {
	// 列表
	public function index() {
		// 检查权限
		A('Privilege')->isLogin();
	
		// 获取数据
		$filter = array(
			'page' => $_GET['page'],
			'user_id' => $_SESSION['id']
		);
		$const = array();
		$ret = D('Common', 'test')->rList($filter, $const);
		$testList = $ret['data'];
		foreach($testList as $key => $test) {
			$testList[$key] = $this->format($test);
		}

		// 分页信息
		$pager['count'] = $ret['count'];
		$pager['cntPage'] = $_GET['page'];
		if (empty($pager['cntPage'])) $pager['cntPage'] = 1;
		$pager['totPage'] =  ceil($pager['count'] / 10);

		$this->assign('pager', $pager);
		$this->assign('testList', $testList);
		
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 创建
	public function create() {
		// 检查权限
		A('Privilege')->isLogin();

		$data['name'] = $_POST['name'];
		if (empty($data['name']))
			$data['name'] = '一场新的测评';
		$ret = D('Common', 'test')->c($data);
		$this->ajaxReturn($ret);
	}
	
	// 设置（基本信息）
	public function setting() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
	
		$test = D('Common', 'test')->r($_GET['test_id']);
		$test = $this->format($test);
		
		$this->assign('test', $test);

		$page['pageTitle'] = '基本信息';
		$page['item1'] = 'setting';
		$page['item2'] = 'basic';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 设置（基本信息）处理
	public function settingDo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
	
		$data['id'] = $_POST['test_id'];
		$data['name'] = $_POST['name'];
		$data['duration'] = $_POST['duration'];
		$data['desc'] = $_POST['desc'];
		
		$error = A('Common')->isCorrect($data, D('Test')->dataRule);
		if (!empty($error['desc']))
			$error['desc_error'] = $error['desc'];
		if (!empty($error)) {
			$ret['status'] = 'fail';
			$ret['error'] = $error;
		} else {
			D('Common', 'test')->u($data);
			$ret['status'] = 'success';
		}
		$ret['name'] = $_POST['name'];
		$this->ajaxReturn($ret);
	}
	
	// 设置（高级）
	public function settingAdvance() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);

		$test = D('Common', 'test')->r($_GET['test_id']);
		$test = $this->format($test);
		if (empty($test['cutoff'])) $test['cutoff'] = '';
		if (empty($test['start_datetime_int'])) {
			$test['start_date'] = '';
			$test['start_date_int'] = '';
			$test['start_time'] = '';
			$test['start_time_int'] = '';
		}
		if (empty($test['end_datetime_int'])) {
			$test['end_date'] = '';
			$test['end_date_int'] = '';
			$test['end_time'] = '';
			$test['end_time_int'] = '';
		}
		
		$needInfo = array(
			'name' => '姓名',
			'phone' => '手机号码',
			'resume' => '简历',
			'other' => '其它',
		);
		//$alloLang = array('C++', 'C', 'Pascal', 'Java', 'PHP', 'Python', 'C#', 'Javascript', 'Ruby', 'Perl', 'Objective C');
		$alloLang = array('C++', 'C', 'Pascal');

		$this->assign('needInfo', $needInfo);
		$this->assign('alloLang', $alloLang);
		$this->assign('test', $test);
		
		$page['pageTitle'] = '高级设置';
		$page['item1'] = 'setting';
		$page['item2'] = 'advance';
		$page['content'] = $this->fetch('settingAdvance');
		$this->ajaxReturn($page);
	}
	
	// 设置（高级）（处理）
	public function settingAdvanceDo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);

		$data = array();
		// 编号
		$data['id'] = $_POST['test_id'];
		// 开始时间
		if (!empty($_POST['start_date']))
			$data['start_datetime_int'] = (int)(timeToInt($_POST['start_date']) + timeToInt('1970-01-01 ' . $_POST['start_time']) + 8 * 3600);
		else
			$data['start_datetime_int'] = 0;
		// 开始时间
		if (!empty($_POST['end_date']))
			$data['end_datetime_int'] = (int)(timeToInt($_POST['end_date']) + timeToInt('1970-01-01 ' . $_POST['end_time']) + 8 * 3600);
		else
			$data['end_datetime_int'] = 0;
		// 自动通过分数
		if (!empty($_POST['cutoff']))
			$data['cutoff'] = $_POST['cutoff'];
		else
			$data['cutoff'] = 0;
		// 答题方式
		$data['answer_type_id'] = $_POST['answer_type_id'];
		// 公开测评
		$data['allow_public'] = $_POST['allow_public'];
		// 需要收集的信息
		$data['need_info'] = '';
		foreach($_POST['need_info'] as $item) {
			$data['need_info'] .= $item . '|';
		}
		// 允许使用的语言
		$data['allow_lang'] = '';
		foreach($_POST['allow_lang'] as $item) {
			$data['allow_lang'] .= $item . '|';
		}
		$para['action'] = 'test';
		$para['retType'] = 'ajax';
		A('Common')->editDo($data, $para);
	}
	
	// 设置（删除）
	public function settingDelete() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
	
		$test = D('Common', 'test')->r($_GET['test_id']);
		$test = $this->format($test);
		
		$this->assign('test', $test);

		$page['pageTitle'] = '删除测评';
		$page['item1'] = 'setting';
		$page['item2'] = 'delete';
		$page['content'] = $this->fetch('settingDelete');
		$this->ajaxReturn($page);
	}
	
	// 设置（删除）处理	
	public function settingDeleteDo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
		
		D('Common', 'test')->d($_POST['test_id']);
		$this->ajaxReturn(array('status' => 'success', 'linkUrl' => '/test/index'));
	}

	// 获取标题
	public function getName() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);

		$test = D('Common', 'test')->r($_GET['test_id']);
		$ret['name'] = $test['name'];
		$this->ajaxReturn($ret);
	}

	// 格式化
	public function format($test) {
		$test['need_info'] = split('\|', $test['need_info']);
		$test['desc_str'] = nl2br($test['desc']);
		$test['start_date'] = intToTime($test['start_datetime_int'], 'Y-m-d');
		$test['start_date_int'] = timeToInt($test['start_date']);
		$test['start_time'] = intToTime($test['start_datetime_int'], 'H:i');
		$test['start_time_int'] = timeToInt($test['start_time']);
		$test['end_date'] = intToTime($test['end_datetime_int'], 'Y-m-d');
		$test['end_date_int'] = timeToInt($test['end_date']);
		$test['end_time'] = intToTime($test['end_datetime_int'], 'H:i');
		$test['end_time_int'] = timeToInt($test['end_time']);
		$test['count_tot'] = $test['count_invited'] + $test['count_running'] + $test['count_completed'] + $test['count_passed'] + $test['count_failed'];
		return $test;
	}
}