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
			if (empty($cd)) {
				$ret['error']['email'] = '您尚未被邀请参与此次测评';
			}
			else
			if ($cd['password'] != $_POST['password']) {
				$ret['error']['password'] = '您的密码不正确';
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
		$test = D('Common', 'test')->r($_POST['test_id']);
		$test = A('Test')->format($test);
		$cd = D('Common', 'candidate')->r($_SESSION['cd_id']);
		if ($cd['status_id'] != 1) die();
	
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
		D('Common', 'candidate')->u($data);
		
		// 跳转
		$ret['status'] = 'success';
		$ret['jumpUrl'] = '/attend/start/id/' . $_POST['test_id'];
		$this->ajaxReturn($ret);
	}

	// 开始测评
	public function start() {
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
		
		$this->assign('test', $test);
		$this->assign('quesCount', $quesCount);
		$this->assign('quesList', $quesList);
		
		$this->display();
	}
	
	// 获取题目
	public function getQues() {
		$ret['base'] = D('Common', 'question')->r($_POST['question_id']);
		if ($ret['base']['type_id'] == 4) {
			$ret['detail'] = D('Common', 'q_program')->r($ret['base']['id']);
			$ret['detail'] = A('QProgram')->format($ret['detail']);
		}
		//dump($ret['detail']);
		$this->ajaxReturn($ret);
	}
	
}