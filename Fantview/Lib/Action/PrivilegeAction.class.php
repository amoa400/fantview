<?php

class PrivilegeAction extends Action {
	// 权限检测结束后的操作
	public function retRes($retType = 'pageJump', $data = '', $url = '') {
		if ($retType == 'pageJump') {
			$ret['linkUrl'] = $url;
			$this->ajaxReturn($ret);
		}	
		if ($retType == 'jump')
			$this->redirect($url);
		if ($retType == 'return')
			return $data;
	}

	// 查看是否登录
	public function isLogin($retType = 'pageJump') {
		if (empty($_SESSION['id']))
			return retRes($retType, false, '/index/login');
		else
			return retRes('return', $_SESSION['id']);
	}
	
	// 
	
}