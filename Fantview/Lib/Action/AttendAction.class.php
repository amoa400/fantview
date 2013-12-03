<?php

class AttendAction extends Action {

	// 登录
    public function login() {
		$test = D('Common', 'test')->r($_GET['id']);
		if (empty($test)) $this->error();
		else {
			$test = A('Test')->format($test);
			$this->assign('test', $test);
			$this->display();
		}
    }
	
	// 公开登录
	public function publicLogin() {
		$test = D('Common', 'test')->r($_GET['id']);
		$test = A('Test')->format($test);
		$this->assign('test', $test);
		$this->display('publicLogin');
	}
	
	// 登录
    public function loginDo() {
		// 查看邮箱和密码是否正确
		$ret = array();
		if (empty($_POST['email']))
			$ret['error']['email'] = '不能为空';
		else
		if (strlen($_POST['email']) > 40)
			$ret['error']['email'] = '长度不能大于40位';
		else
		if (!validEmail($_POST['email']))
			$ret['error']['email'] = '格式不正确';
		if (empty($_POST['password']))
			$ret['error']['password'] = '不能为空';
		else
		if (strlen($_POST['password']) > 10)
			$ret['error']['password'] = '长度不能大于10位';		
		
		// 从数据库获取
		if (empty($ret)) {
			$cd = D('Common', 'candidate')->rBySql(array('test_id' => $_POST['test_id'], 'email' => $_POST['email']));
			$test = D('Common', 'test')->r($_POST['test_id']);
			if (empty($cd)) {
				$ret['error']['email'] = '您尚未被邀请参与此次测评';
			}
			else if ($cd['password'] != $_POST['password']) {
				$ret['error']['password'] = '您的密码不正确';
			}
			else if ($cd['status_id'] >= 3) {
				$ret['error']['email'] = '您已提交过此次测评，请耐心等待邮件通知';
			}
			else if ($test['start_datetime_int'] != 0 && time() < $test['start_datetime_int']) {
				$ret['error']['email'] = '测评尚未开始，开始时间：'.date("Y-m-d H:i:s", $test['start_datetime_int']);
			}
			else if ($test['end_datetime_int'] != 0 && time() > $test['end_datetime_int']) {
				$ret['error']['email'] = '测评已经结束';
			}
		}		
		
		// 返回结果
		if (!empty($ret))
			$ret['status'] = 'fail';
		else {
			$ret['status'] = 'success';
			$ret['jumpUrl'] = '/attend/fill/id/' . $_POST['test_id'];
			$this->loginSucceed($_POST['test_id'], $cd['id']);
		}
		$this->ajaxReturn($ret);
	}
	
	// 公开登录
    public function publicLoginDo() {
		// 查看邮箱和密码是否正确
		$ret = array();
		if (empty($_POST['email']))
			$ret['error']['email'] = '不能为空';
		else
		if (strlen($_POST['email']) > 40)
			$ret['error']['email'] = '长度不能大于40位';
		else
		if (!validEmail($_POST['email']))
			$ret['error']['email'] = '格式不正确';
		$test_id = $_POST['test_id'];
		$test = D('Common', 'test')->r($test_id);
		if (!$test || $test['allow_public'] != 2) {
			$ret['status'] = 'error';
			$ret['error'] = '该评测非公开，即将跳转登陆页...';
			$ret['jumpUrl'] = '/attend/login/id/' . $test_id;
			$this->ajaxReturn($ret);
		}		
		
		// 从数据库获取
		if (empty($ret)) {
			$res = A('Report')->inviteDo($_POST['test_id'], $_POST['email'], 'return');
			if ($res['status'] != 'success') {
				$ret = $res;
			} else {
				$ret['tip']['email'] = '邀请邮件已发送至您的邮箱，请查看邮件获取密码';
				$ret['status'] = 'success';
			}
		}
		else {
			$ret['status'] = 'fail';
		}
		
		$this->ajaxReturn($ret);
	}
	
	// 成功登陆
	public function loginSucceed($test_id, $cd_id) {
		if (empty($_SESSION['attend'])) {
			$_SESSION['attend'] = array(
				$test_id 	=>	$cd_id,
			);
		}
		else $_SESSION['attend'][$test_id] = $cd_id;
	}
	
	// 登出
	public function logout($test_id, $cd_id) {
		if ($_SESSION['attend'][$test_id] == $cd_id) {
			unset($_SESSION['attend'][$test_id]);
		}
	}

	// 填入信息
	public function fill() {
		// 检查权限
		$test_id = $_GET['id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id);
		
		$test_raw = D('Common', 'test')->r($test_id);
		$test = A('Test')->format($test_raw);
		$cd = D('Common', 'candidate')->r($cd_id);
		if ($cd['status_id'] != 1)
			$this->redirect('/attend/start/id/'.$test_id);
		$this->assign('test', $test);
		$this->display();
	}
	
	// 填入信息（处理）
	public function fillDo() {
		// 检查权限
		$test_id = $_POST['test_id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id);
		
		$test_raw = D('Common', 'test')->r($test_id);
		$test = A('Test')->format($test_raw);
		$cd = D('Common', 'candidate')->r($cd_id);
		
		// 检查是否已填过信息
		if ($cd['status_id'] != 1) {
			$ret['status'] = 'success';
			$ret['jumpUrl'] = '/attend/start/id/'.$test_id;
			$this->ajaxReturn($ret);
		}		
		
		// 更新候选人信息
		$data_candidate = array();
		$ret = array();
		
		// 检查字段是否正确
		foreach($test['need_info'] as $item) {
			if (empty($item)) continue;			
			
			// 姓名
			if ($item == 'name') {
				if (empty($_POST[$item]))
					$ret['error'][$item] = '不能为空';
				else
				if (strlen($_POST[$item]) > 20)
					$ret['error'][$item] = '格式不正确';
			}
			
			// 手机
			if ($item == 'phone') {
				if (empty($_POST[$item]))
					$ret['error'][$item] = '不能为空';
				else
				if (strlen($_POST[$item]) > 20)
					$ret['error'][$item] = '格式不正确';
			}
			
			// 简历
			if ($item == 'resume') {
				import('ORG.Net.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize  = 5 * 1024 * 1024;
				$upload->allowExts  = array('pdf');
				$upload->savePath =  '../Upload/' . $test['user_id'] . '/temp/';
				if(!$upload->upload())
					$ret['error'][$item] =  $upload->getErrorMsg();
				$info = $upload->getUploadFileInfo();
				$uploadInfo = $info[0];
				$data_candidate['extension'] = $uploadInfo['extension'];
			}
			
			// 储存信息
			if ($item != 'resume')
				$data_candidate[$item] = $_POST[$item];
		}
		
		// 检查是否承诺不作弊
		if ($_POST['promise'] != 1)
			$ret['error']['promise'] = '请承诺不作弊';
		
		// 存在错误
		if (!empty($ret)) {
			unlink('../Upload/' . $test['user_id'] . '/temp/' . $uploadInfo['savename']);
			$ret['status'] = 'fail';
			$this->ajaxReturn($ret);
		}
		
		// 更新信息
		rename('../Upload/' . $test['user_id'] . '/temp/' . $uploadInfo['savename'], '../Upload/' . $test['user_id'] . '/candidate/' . $cd['id'] . '.' .$data_candidate['extension']);
		$data_candidate['id'] = $cd_id;
		$data_candidate['status_id'] = 2;
		$data_candidate['start_time_int'] = time();
		if ($test_raw['end_datetime_int'] != 0 && $data_candidate['start_time_int']+$test_raw['duration']*60 > $test_raw['end_datetime_int']) {
			$data_candidate['end_time_int'] = $test_raw['end_datetime_int'];
		}
		else {
			$data_candidate['end_time_int'] = $data_candidate['start_time_int']+$test_raw['duration']*60;
		}
		$data_candidate['tot_time_int'] = $data_candidate['end_time_int'] - $data_candidate['start_time_int'];
		$data_candidate['ip'] = getIp();
		D('Common', 'candidate')->u($data_candidate);
		
		// 将最晚结束时间传给评测机
		$timeFormat = date('Y|m|d|H|i|', $data_candidate['end_time_int']+60);
		sendSocket('endtime|'.$cd_id.'|'.$timeFormat);
		
		// 更新统计数据
		$data_test['id'] = $test_raw['id'];
		$data_test['count_invited'] = $test_raw['count_invited']-1;
		$data_test['count_running'] = $test_raw['count_running']+1;
		D('Common', 'test')->u($data_test);
		
		// 跳转
		$ret['status'] = 'success';
		$ret['jumpUrl'] = '/attend/start/id/' . $_POST['test_id'];
		$this->ajaxReturn($ret);
	}

	// 开始测评
	public function start() {
		// 检查权限
		$test_id = $_GET['id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id);

		$test_raw = D('Common', 'test')->r($test_id);
		$test = A('Test')->format($test_raw);
		$tmpList = D('Common', 'test_question')->rList(array('test_id' => $test_id, 'page' => 'all'), array('order' => array('field' => 'rank', 'type' => 'ASC')));
		$idList = array();
		
		foreach($tmpList['data'] as $item) {
			$idList[] = $item['question_id'];
			
			// 将初始答案填入数据库（基本信息）
			$data['candidate_id'] = $cd_id;
			$data['question_id'] = $item['question_id'];
			$data['answer'] = "";
			$data['tot_time_int'] = 0;
			$data['score'] = 0;
			$data['status_id'] = 1;
			$ret = D('Common', 'answer')->c($data);
		}
		$tmpList = D('Common', 'question')->rList(array('id' => $idList, 'page' => 'all'));
		$quesCount = $tmpList['count'];
		$quesList = array();
		foreach($idList as $item) {
			foreach($tmpList['data'] as $item2) {
				if ($item == $item2['id']) {
					$quesList[] = $item2;
					break;
				}
			}
		}
		
		$candidate = D('Common', 'candidate')->r($cd_id);
		$timeLeft = $candidate['end_time_int'] - time();
		
		$this->assign('candidate', $candidate);
		$this->assign('time_left', $timeLeft);
		$this->assign('test', $test);
		$this->assign('quesCount', $quesCount);
		$this->assign('quesList', $quesList);
		
		$this->display();
	}
	
	// 获取题目
	public function getQues() {
		// 检查权限
		$test_id = $_POST['test_id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id, 'pageJump'); 
		
		// 获取题目基本信息
		$ret['base'] = D('Common', 'question')->r($_POST['question_id']);
		
		// 获取候选人答题信息
		$sql = array();
		$sql['question_id'] = $_POST['question_id'];
		$sql['candidate_id'] = $_SESSION['attend'][$test_id];
		$res = D('Common', 'answer')->rBySql($sql);
		if ($res) {
			$ret['answer'] = $res['answer'];
			$ret['tot_time'] = $res['tot_time_int'];
		}
		else {
			$ret['answer'] = "";
			$ret['tot_time'] = 0;
		}
		
		// 获取题目特殊信息
		if ($ret['base']['type_id'] == 4) {
			$ret['detail'] = D('Common', 'q_program')->r($ret['base']['id']);
			$ret['detail'] = A('QProgram')->format($ret['detail']);
			if ($res = D('Common', 'a_program')->rBySql($sql)) {
				$langArr = array(
					1			=>		'C'	,
					2			=>		'C++',
					3			=>		'Pascal',
					4			=>		'Java',
					5			=>		'PHP',
				);
				$ret['lang'] = $langArr[$res['lang']];
			}
			else $ret['lang'] = 'C';
		}
		else if ($ret['base']['type_id'] == 1 || $ret['base']['type_id'] == 2) {
			if ($ret['base']['type_id'] == 1) $ret['detail'] = D('Common', 'q_single')->r($ret['base']['id']);
			else if ($ret['base']['type_id'] == 2) $ret['detail'] = D('Common', 'q_multi')->r($ret['base']['id']);
			$ret['detail']['option'] = explode('|-|$|', $ret['detail']['option']);
			foreach ($ret['detail']['option'] as $key => &$value) {
				if (!$value) {
					unset($ret['detail']['option'][$key]);
					break;
				}
				$value = array(
					chr($key+65),
					$value
				);
			}
			$ret['detail']['answer'] = "";
		}
		else if ($ret['base']['type_id'] == 3) {
			$ret['detail'] = D('Common', 'q_qa')->r($ret['base']['id']);
		}
		
		$this->ajaxReturn($ret);
	}

	// 保存答案
	public function saveAns() {
		// 检查权限
		$test_id = $_POST['test_id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id, 'pageJump');		
		
		// 插入数据库（基本信息）
		$data['candidate_id'] = $cd_id;
		$data['question_id'] = $_POST['question_id'];
		$data['answer'] = $_POST['answer'];
		$data['tot_time_int'] = $_POST['tot_time'];
		$data['score'] = 0;
		$data['status_id'] = 1;
		$ret = D('Common', 'answer')->c($data);
		if (!$ret) D('Common', 'answer')->u($data);		
		
		// 插入数据库（特殊信息）
		if ($_POST['type'] == 4) {
			$data2['candidate_id'] = $cd_id;
			$data2['question_id'] = $_POST['question_id'];
			$ques = D('Common', 'q_program')->r($_POST['question_id']);
			$data2['time_limit'] = $ques['time_limit'];
			$data2['memory_limit'] = $ques['memory_limit'];
			$langArr = array(
				'C'				=>		1,
				'C++'			=>		2,
				'Pascal'		=>		3,
				'Java'			=>		4,
				'PHP'			=>		5,
			);
			$data2['lang'] = $langArr[$_POST['lang']];
			$data2['status_id'] = 1;
			$ret = D('Common', 'a_program')->c($data2);
			if (!$ret) D('Common', 'a_program')->u($data2);
		}
		
		$candidate = D('Common', 'candidate')->r($cd_id);
		$this->ajaxReturn($candidate['end_time_int']-time());
	}

	// 提交答案
	public function submit() {
		// 检查权限
		$test_id = $_POST['test_id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id);	
		$this->logout($test_id, $cd_id);

		// 更新候选人数据
		$candidate = D('Common', 'candidate')->r($cd_id);
		$data['id'] = $cd_id;
		$data['tot_time_int'] = time()-$candidate['start_time_int'];
		$data['end_time_int'] = time();
		$data['status_id'] = 3;
		$ret = D('Common', 'candidate')->u($data);
		
		$test_raw = D('Common', 'test')->r($_POST['test_id']);
		$test = A('Test')->format($test_raw);

		// 更新统计数据
		$data_test['id'] = $test_raw['id'];
		$data_test['count_running'] = $test_raw['count_running']-1;
		$data_test['count_completed'] = $test_raw['count_completed']+1;
		D('Common', 'test')->u($data_test);
		
		// 向评测机发送请求
		sendSocket('eval|'.$cd_id.'|');
		$this->assign('test', $test);
		$this->display();
	}

	// 编译运行
	public function comRun() {
		// 检查权限
		$test_id = $_POST['test_id'];
		$cd_id = A('Privilege')->attend_isLogin($test_id, 'pageJump');
		
		// 插入数据库（基本信息）
		$data['candidate_id'] = $_SESSION['attend'][$test_id];
		$data['question_id'] = $_POST['question_id'];
		$data['answer'] = $_POST['code'];
		$ret = D('Common', 'answer')->c($data);
		if (!$ret) D('Common', 'answer')->u($data);
		
		// 插入数据库（特殊信息）
		$data2['candidate_id'] = $_SESSION['attend'][$test_id];
		$data2['question_id'] = $_POST['question_id'];
		$ques = D('Common', 'q_program')->r($_POST['question_id']);
		$data2['time_limit'] = $ques['time_limit'];
		$data2['memory_limit'] = $ques['memory_limit'];
		$langArr = array(
			'C'				=>		1,
			'C++'			=>		2,
			'Pascal'		=>		3,
			'Java'			=>		4,
			'PHP'			=>		5,
		);
		$data2['lang'] = $langArr[$_POST['lang']];
		$data2['status_id'] = 2;
		$ret = D('Common', 'a_program')->c($data2);
		if (!$ret) D('Common', 'a_program')->u($data2);
		
		// 发送SOCKET
		set_time_limit(30);
		$res = sendSocket('cprun|' . $_SESSION['attend'][$test_id] . '|'. (int)$_POST[question_id] . '|', true);
		$ans = D('Common', 'a_program')->rBySql(array('candidate_id' => $_SESSION['attend'][$test_id], 'question_id' => $_POST[question_id]));
		$this->ajaxReturn($ans['result']);

	}

	// 错误信息
	public function error($type = 0, $test_id = 0) {
		switch ($type) {
			case 0:
				$info = "你所访问的测评不存在，请核对网址！";
				break;
			case 1:
				$info = "你还没有登录！<span id='timer'>3</span>秒后转到登录页...";
				break;
		}
		$this->assign('type', $type);
		$this->assign('info', $info);
		$this->assign('test_id', $test_id);
		$this->display('error');
	}
	
}