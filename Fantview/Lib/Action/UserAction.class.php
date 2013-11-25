<?php

// 已加权限

class UserAction extends Action {

	// 编辑信息
	public function edit() {
		// 检查权限
		A('Privilege')->isLogin();

		$user = D('Common', 'user')->r($_SESSION['id']);
		
		$this->assign('user', $user);
		
		$page['pageTitle'] = '我的资料';
		$page['item1'] = 'user';
		$page['item2'] = 'edit';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 编辑信息（处理）
	public function editDo() {
		// 检查权限
		A('Privilege')->isLogin();
		
		$data['id'] = $_SESSION['id'];
		$data['name'] = $_POST['name'];
		$data['company_name'] = $_POST['company_name'];
		$data['phone'] = $_POST['phone'];
		$dataClass = D('Common', 'user');
		$dataClass->u($data);
		if (!empty($dataClass->errorInfo)) {
			$ret['status'] = 'fail';
			if (!empty($dataClass->errorInfo))
				$ret['error'] = $dataClass->errorInfo;
			$this->ajaxReturn($ret);
		} else {
			$_SESSION['name'] = $_POST['name'];
			$this->ajaxReturn(array('status' => 'success', 'js' => '$(".header .user .name").html("' . $_POST['name'] . '");windowResize();'));
		}
	}
	
	// 编辑头像
	public function editPhoto() {
		// 检查权限
		A('Privilege')->isLogin();
		
		$user = D('Common', 'user')->r($_SESSION['id']);
		
		$this->assign('user', $user);
		
		$page['pageTitle'] = '修改头像';
		$page['item1'] = 'user';
		$page['item2'] = 'edit_photo';
		$page['content'] = $this->fetch('editPhoto');
		$this->ajaxReturn($page);
	}
	
	// 编辑头像
	public function editPhotoDo() {
		// 检查权限
		A('Privilege')->isLogin();
		
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();
		$upload->maxSize  = 2 * 1024 * 1024;
		$upload->allowExts  = array('jpg', 'jpeg', 'png');
		$upload->savePath =  './user/' . $_SESSION['id'] . '/';
		if(!$upload->upload())
			$this->ajaxReturn(array('status' => 'fail', 'error' => array('photo' => $upload->getErrorMsg())));
		$info = $upload->getUploadFileInfo();
		$info = $info[0];
		
		// 压缩图像
		ImageToJPG('./user/' . $_SESSION['id'] . '/' . $info['savename'], './user/' . $_SESSION['id'] . '/' .'photo.jpg', 100, 100);
		unlink('./user/' . $_SESSION['id'] . '/' . $info['savename']);

		$this->ajaxReturn(array('status' => 'success', 'js' => '$(".header .user .photo img, .upload_photo img").attr("src", "/user/' . $_SESSION['id'] . '/photo.jpg?r=' . randomChar() . '");'));
	}
	
	// 修改密码
	public function editPass() {
		// 检查权限
		A('Privilege')->isLogin();
		
		$page['pageTitle'] = '修改密码';
		$page['item1'] = 'user';
		$page['item2'] = 'edit_pass';
		$page['content'] = $this->fetch('editPass');
		$this->ajaxReturn($page);
	}
	
	// 修改密码（处理）
	public function editPassDo() {
		// 检查权限
		A('Privilege')->isLogin();
		
		$user = D('Common', 'user')->r($_SESSION['id']);
		$ret = array();
		if (encrypt($_POST['old_pass']) != $user['password'])
			$ret['error']['old_pass'] = '密码不正确';
		if (strlen($_POST['new_pass']) < 6 || strlen($_POST['new_pass']) > 30)
			$ret['error']['new_pass'] = '密码长度应为6-30位';
		if ($_POST['new_pass'] != $_POST['re_pass'])
			$ret['error']['re_pass'] = '两次输入密码不一致';

		if (empty($ret)) {
			$data['id'] = $_SESSION['id'];
			$data['password'] = encrypt($_POST['new_pass']);
			D('Common', 'user')->U($data);
			
			$ret['status'] = 'success';
			$ret['refresh'] = 1;
			$this->ajaxReturn($ret);
		} else {
			$ret['status'] = 'fail';
			$this->ajaxReturn($ret);
		}
	}
}