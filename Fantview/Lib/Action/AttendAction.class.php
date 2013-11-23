<?php

class AttendAction extends Action {

	// 登录
    public function login() {
		$test = D('Common', 'test')->r($_GET['id']);
		$test = A('Test')->format($test);
		$this->assign('test', $test);
		$this->display();
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
			else if ($test['start_datetime_int'] != 0 && time() > $test['end_datetime_int']) {
				$ret['error']['email'] = '测评已经结束';
			}

		}
		
		// 返回结果
		if (!empty($ret))
			$ret['status'] = 'fail';
		else {
			$ret['status'] = 'success';
			$ret['jumpUrl'] = '/attend/fill/id/' . $_POST['test_id'];
			$this->loginSucceed($cd['id']);
		}
		$this->ajaxReturn($ret);
	}
	
	// 成功登陆
	public function loginSucceed($cd_id) {
		$_SESSION['cd_login'] = 1;
		$_SESSION['cd_id'] = $cd_id;
	}
	
	// 登出
	public function logout($test_id, $cd_id) {
		unset($_SESSION['cd_login']);
		unset($_SESSION['cd_id']);
	}

	// 填入信息
	public function fill() {
		$test = D('Common', 'test')->r($_GET['id']);
		$test = A('Test')->format($test);
		$cd = D('Common', 'candidate')->r($_SESSION['cd_id']);
		if ($cd['status_id'] != 1)
			$this->redirect('/attend/start/id/' . $_GET['id']);
		$this->assign('test', $test);
		$this->display();
	}
	
	// 填入信息（处理）
	public function fillDo() {
		$test2 = D('Common', 'test')->r($_POST['test_id']);
		$test = A('Test')->format($test2);
		$cd = D('Common', 'candidate')->r($_SESSION['cd_id']);
		if ($cd['status_id'] != 1) {
			$ret['status'] = 'success';
			$ret['jumpUrl'] = '/attend/start/id/' . $_POST['test_id'];
			$this->ajaxReturn($ret);
		}
	
		$data = array();
		$data['id'] = $_SESSION['cd_id'];
		$data['status_id'] = 2;
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
				$upload->allowExts  = array('doc', 'docx', 'pdf', 'txt');
				$upload->savePath =  '../Upload/' . $test['user_id'] . '/temp/';
				if(!$upload->upload())
					$ret['error'][$item] =  $upload->getErrorMsg();
				$info = $upload->getUploadFileInfo();
				$uploadInfo = $info[0];
				$data['extension'] = $uploadInfo['extension'];
			}

			// 储存信息
			if ($item != 'resume')
				$data[$item] = $_POST[$item];
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
		rename('../Upload/' . $test['user_id'] . '/temp/' . $uploadInfo['savename'], '../Upload/' . $test['user_id'] . '/candidate/' . $cd['id'] . '.' .$data['extension']);
		$data['start_time_int'] = time();
		if ($test2['end_datetime_int'] != 0 && $data['start_time_int']+$test2['duration']*60 > $test2['end_datetime_int']) $data['end_time_int'] = $test2['end_datetime_int'];
		else $data['end_time_int'] = $data['start_time_int']+$test2['duration']*60;
		$data['tot_time_int'] = $data['end_time_int'] - $data['start_time_int'];
		$data['ip'] = getIp();
		D('Common', 'candidate')->u($data);
		$timeFormat = date('Y|m|d|H|i|', $data['end_time_int']+60);
		sendSocket('endtime|'.$_SESSION['cd_id'].'|'.$timeFormat);
		
		$data2['id'] = $test2['id'];
		$data2['count_invited'] = $test2['count_invited']-1;
		$data2['count_running'] = $test2['count_running']+1;
		D('Common', 'test')->u($data2);
		// 跳转
		$ret['status'] = 'success';
		$ret['jumpUrl'] = '/attend/start/id/' . $_POST['test_id'];
		$this->ajaxReturn($ret);
	}

	// 开始测评
	public function start() {
		if (!isset($_SESSION['cd_login'])) $this->redirect('/attend/login/id/'.$_GET['id']);
		$test = D('Common', 'test')->r($_GET['id']);
		$test = A('Test')->format($test);

		$tmpList = D('Common', 'test_question')->rList(array('test_id' => $_GET['id'], 'page' => 'all'), array('order' => array('field' => 'rank', 'type' => 'ASC')));
		$idList = array();
		foreach($tmpList['data'] as $item) {
			$idList[] = $item['question_id'];
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
		$candidate = D('Common', 'candidate')->r($_SESSION['cd_id']);
		$time_left = $candidate['end_time_int'] - time();
		$this->assign('candidate', $candidate);
		$this->assign('time_left', $time_left);
		$this->assign('test', $test);
		$this->assign('quesCount', $quesCount);
		$this->assign('quesList', $quesList);
		
		$this->display();
	}
	
	// 获取题目
	public function getQues() {
		$ret['base'] = D('Common', 'question')->r($_POST['question_id']);
		$sql = array();
		$sql['question_id'] = $_POST['question_id'];
		$sql['candidate_id'] = $_SESSION['cd_id'];
		$res = D('Common', 'answer')->rBySql($sql);
		if ($res) {
			$ret['answer'] = $res['answer'];
			$ret['tot_time'] = $res['tot_time_int'];
		}
		else {
			$ret['answer'] = "";
			$ret['tot_time'] = 0;
		}
		if ($ret['base']['type_id'] == 4) {
			$ret['detail'] = D('Common', 'q_program')->r($ret['base']['id']);
			$ret['detail'] = A('QProgram')->format($ret['detail']);
			if ($res = D('Common', 'a_program')->rBySql($sql)) {
				$langArr = array(
					1			=>		'C'	,
					2			=>		'C++',
					3			=>		'Pascal',
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
			//print_r($ret);
		}
		else if ($ret['base']['type_id'] == 3) {
			$ret['detail'] = D('Common', 'q_qa')->r($ret['base']['id']);
		}
		$this->ajaxReturn($ret);
	}

	// 保存答案
	public function saveAns() {
		// 插入数据库（基本信息）
		$data['candidate_id'] = $_SESSION['cd_id'];
		$data['question_id'] = $_POST['question_id'];
		$data['answer'] = $_POST['answer'];
		$data['tot_time_int'] = $_POST['tot_time'];
		$data['score'] = 0;
		$data['status_id'] = 1;
		$ret = D('Common', 'answer')->c($data);
		if (!$ret) D('Common', 'answer')->u($data);
		// 插入数据库（特殊信息）
		if ($_POST['type'] == 4) {
			$data2['candidate_id'] = $_SESSION['cd_id'];
			$data2['question_id'] = $_POST['question_id'];
			$ques = D('Common', 'q_program')->r($_POST['question_id']);
			$data2['time_limit'] = $ques['time_limit'];
			$data2['memory_limit'] = $ques['memory_limit'];
			$langArr = array(
				'C'				=>		1,
				'C++'			=>		2,
				'Pascal'		=>		3,
			);
			$data2['lang'] = $langArr[$_POST['lang']];
			$data2['status_id'] = 1;
			$ret = D('Common', 'a_program')->c($data2);
			if (!$ret) D('Common', 'a_program')->u($data2);
		}
		$candidate = D('Common', 'candidate')->r($_SESSION['cd_id']);
		$this->ajaxReturn($candidate['end_time_int']-time());
	}

	// 提交答案
	public function submit() {
		if (!isset($_SESSION['cd_login'])) $this->redirect('/attend/login/id/'.$_GET['id']);
		$candidate = D('Common', 'candidate')->r($_SESSION['cd_id']);
		$data['id'] = $_SESSION['cd_id'];
		$data['tot_time_int'] = time()-$candidate['start_time_int'];
		$data['end_time_int'] = time();
		$data['status_id'] = 3;
		$ret = D('Common', 'candidate')->u($data);
		$test2 = D('Common', 'test')->r($_POST['test_id']);
		$test = A('Test')->format($test2);

		$data2['id'] = $test2['id'];
		$data2['count_running'] = $test2['count_running']-1;
		$data2['count_completed'] = $test2['count_completed']+1;
		D('Common', 'test')->u($data2);
		sendSocket('eval|'.$_SESSION['cd_id'].'|');
		$this->logout();
		$this->assign('test', $test);
		$this->display();
	}

	// 评测答案
	public function judge() {
		$candidate_id = $_SESSION['cd_id'];
		// 更新candidate表的status_id
		$data1['id'] = $candidate_id;
		$data1['status_id'] = 3;
		$ret = D('Common', 'candidate')->u($data1);
		// 选择题评分
		$filter = array(
			'candidate_id'	=>	$candidate_id,
			'status_id'		=>	1,
			'page'			=>	'all',
		);
		$const = array(
			'order'			=>	array(
				'field' 	=> 'question_id', 
				'type' 		=> 'ASC',
			),
		);
		$answer = D('Common', 'answer')->rList($filter, $const);
		foreach ($answer['data'] as $item) {
			$q_id = $item['question_id'];
			$q_info = D('Common', 'question')->r($q_id);
			//dump($question_info);
			if ($q_info['type_id'] == 1 || $q_info['type_id'] == 2) {
				if ($q_info['type_id'] == 1) $action = 'q_single';
				else $action = 'q_multi';
				$q_detail = D('Common', $action)->r($item['question_id']);
				if ($item['answer'] == $q_detail['answer']) {
					$item['score'] = $q_info['score'];
					$item['status_id'] = 2;
				}
				else {
					$item['score'] = 0;
					$item['status_id'] = 3;
				}
				$ret = D('Common', 'answer')->u($item);
			}
			else if ($q_info['type_id'] == 3) {
				$item['status_id'] = 4;
				$ret = D('Common', 'answer')->u($item);
			}
			else if ($q_info['type_id'] == 4) {
				$sql = array(
					'candidate_id'	=>	$candidate_id,
					'question_id'	=>	$item['question_id'],
				);
				$a_detail = D('Common', 'a_program')->rBySql($sql);
				$a_detail['status_id'] = 3;
				$ret = D('Common', 'a_program')->u($a_detail);
			}
		}
		$flag = true;
		while(1) {
			foreach ($answer['data'] as $item) {
				$q_id = $item['question_id'];
				$q_info = D('Common', 'question')->r($q_id);
				if ($q_info['type_id'] == 4) {
					$sql = array(
						'candidate_id'	=>	$candidate_id,
						'question_id'	=>	$item['question_id'],
					);
					$a_detail = D('Common', 'a_program')->rBySql($sql);
					if ($a_detail['status_id'] == 3 || $a_detail['status_id'] == 4) {
						$flag = false;
						break;
					}
				}
			}
			if ($flag) break;
			echo "NO";
			sleep(5);
		}

	}
	// 编译运行
	public function comRun() {
		// 插入数据库（基本信息）
		$data['candidate_id'] = $_SESSION['cd_id'];
		$data['question_id'] = $_POST['question_id'];
		$data['answer'] = $_POST['code'];
		$ret = D('Common', 'answer')->c($data);
		if (!$ret) D('Common', 'answer')->u($data);
		// 插入数据库（特殊信息）
		$data2['candidate_id'] = $_SESSION['cd_id'];
		$data2['question_id'] = $_POST['question_id'];
		$ques = D('Common', 'q_program')->r($_POST['question_id']);
		$data2['time_limit'] = $ques['time_limit'];
		$data2['memory_limit'] = $ques['memory_limit'];
		$langArr = array(
			'C'				=>		1,
			'C++'			=>		2,
			'Pascal'		=>		3,
		);
		$data2['lang'] = $langArr[$_POST['lang']];
		$data2['status_id'] = 2;
		$ret = D('Common', 'a_program')->c($data2);
		if (!$ret) D('Common', 'a_program')->u($data2);
		// 发送SOCKET
		set_time_limit(30);
		$res = sendSocket('cprun|' . $_SESSION['cd_id'] . '|'. (int)$_POST[question_id] . '|', true);
		$ans = D('Common', 'a_program')->rBySql(array('candidate_id' => $_SESSION['cd_id'], 'question_id' => $_POST[question_id]));
		$this->ajaxReturn($ans['result']);

	}
	
}