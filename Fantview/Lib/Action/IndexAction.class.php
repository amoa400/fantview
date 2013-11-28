<?php

class IndexAction extends Action {

    public function index() {
		$this->display();
    }
	
	public function about() {
		$this->assign('pageTitle', '关于我们');
		$this->display();
	}
	
	public function join() {
		$this->assign('pageTitle', '加入我们');
		$this->display();
	}
	
	public function help() {
		$this->assign('pageTitle', '帮助中心');
		$this->display();
	}
	
	// 登录
	public function login() {
		$this->display();
	}
	
	// 登录
	public function loginDo() {
		$user = D('Common', 'user')->getByField('email', $_POST['email']);
		if (encrypt($_POST['password']) == $user['password'] && $user['type_id'] != 100) {
			$this->loginSucceed($user['id']);
			$ret['status'] = 'success';
			$ret['jumpUrl'] = '/home';
		} else {
			$ret['status'] = 'fail';
		}
		$this->ajaxReturn($ret);
	}
	
	// 注册
	public function register() {
		$this->display();
	}
	
	// 注册处理
	public function registerDo() {
		$para['action'] = 'user';
		$user = getArray($_POST, array('email', 'name', 'password'));
		$user['type_id'] = 100;
		$user['password'] = encrypt($user['password']);
		$res = A('Common')->createDo($user, $para);
		if ($res['status'] == 'success')  {
			// 建立用户文件夹
			mkdir('./user/' . $res['id']);
			// 复制头像
			copy('./image/photo.jpg', './user/' . $res['id'] . '/photo.jpg');
			// 建立上传文件夹
			mkdir('../Upload/' . $res['id']);
			mkdir('../Upload/' . $res['id'] . '/candidate');

			// 登录
			$this->loginSucceed($res['id']);
			$mailCont = A('Common')->getMail('激活 - 加入fantview', $_POST['name'], '欢迎注册fantview，请点击下面的链接完成验证，立即开始面试！', C('ROOT_URL') . '/index/active/p/'. encrypt1('activePass', $_POST['email']));
			sendEmail($_POST['email'], '欢迎注册fantview，马上验证邮箱', $mailCont);
			$res['jumpUrl'] = '/index/bind/id/' . $res['id'];
		}
		$this->ajaxReturn($res);
	}

	// 登录成功
    public function loginSucceed($user_id) {
		$user = D('Common', 'user')->r($user_id);
		$_SESSION = array();
		$_SESSION['id'] = $user['id'];
		$_SESSION['name'] = $user['name'];
	}

	// 登出
	public function logout() {
		unset($_SESSION['id']);
		unset($_SESSION['name']);
		unset($_SESSION['pri']);
		$this->redirect('/');
	}
	
	// 验证
	public function bind() {
		$user = D('Common', 'user')->r($_GET['id']);
		$mailUrl = 'http://mail.' . substr($user['email'], strpos($user['email'], '@') + 1);
		$this->assign('user', $user);
		$this->assign('mailUrl', $mailUrl);
		$this->display();
		
	}	
	
	// 验证处理
	public function active() {
		$email = decrypt1('activePass', $_GET['p']);
		$user = D('Common', 'user')->getByField('email', $email);
		if (empty($user)) $this->error('验证地址错误', '/');
		if ($user['type_id'] != 100) $this->error('该用户已验证过邮箱', '/');
		$this->loginSucceed($user['id']);
		$data['id'] = $user['id'];
		$data['type_id'] = 1;
		D('Common', 'user')->u($data);
		$this->display();
	}
	
}