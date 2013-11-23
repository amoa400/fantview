<?php

class PrivilegeAction extends Action {
	public $checked = array();

	// 权限检测结束后的操作
	public function retRes($retType, $data = '', $url = '/privilege/error') {
		if ($retType == 'pageJump') {
			$ret['status'] = 'fail';
			$ret['jumpUrl'] = $url;
			$this->ajaxReturn($ret);
		}
		if ($retType == 'pageLink') {
			$ret['status'] = 'fail';
			$ret['linkUrl'] = $url;
			$this->ajaxReturn($ret);
		}
		if ($retType == 'jump')
			$this->redirect($url);
		if ($retType == 'return')
			return $data;
	}
	
	// 错误页
	public function error() {
		$page['pageTitle'] = '权限不足';
		$page['item2'] = '';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}

	// 查看是否登录
	public function isLogin($retType = 'pageJump') {
		// 是否已经检测过
		$flag  = -1;
		if (!empty($this->checked['login']))
			$flag  = $this->checked['login'];
		
		// 检测权限
		if ($flag == -1) {
			if (empty($_SESSION['id']))
				$flag = 0;
			else
				$flag = 1;
		}
		
		// 返回结果
		$this->checked['login'] = $flag;
		if ($flag == 0)
			return $this->retRes($retType, false, '/index/login');
		else
			return $this->retRes('return', $_SESSION['id']);
	}
	
	// 是否拥有测试
	public function haveTest($testId, $retType = 'pageLink') {
		// 是否已经检测过
		$flag  = -1;
		if (!empty($this->checked['test'][$testId]))
			$flag  = $this->checked['test'][$testId];
		
		// 检测权限
		if ($flag == -1) {
			// 从数据库中读取
			if (!in_array($testId, $_SESSION['pri']['testIdList'])) {
				$test = D('Common', 'test')->r($testId);
				if ($test['user_id'] == $_SESSION['id'])
					$_SESSION['pri']['testIdList'][] = $test['id'];
			}
			// 检测权限
			if (!in_array($testId, $_SESSION['pri']['testIdList']))
				$flag = 0;
			else
				$flag = 1;
		}
		
		// 返回结果
		$this->checked['test'][$testId] = $flag;
		if ($flag == 0)
			return $this->retRes($retType, false);
		else
			return $this->retRes('return', true);
	}
	
	// 是否拥有问题
	public function haveQues($quesId, $retType = 'pageLink') {
		// 是否已经检测过
		$flag  = -1;
		if (!empty($this->checked['ques'][$quesId]))
			$flag  = $this->checked['ques'][$quesId];
		
		// 检测权限
		if ($flag == -1) {
			// 从数据库中读取
			if (!in_array($quesId, $_SESSION['pri']['quesIdList'])) {
				$ques = D('Common', 'question')->r($quesId);
				if ($ques['user_id'] == $_SESSION['id'])
					$_SESSION['pri']['quesIdList'][] = $ques['id'];
			}
			// 检测权限
			if (!in_array($quesId, $_SESSION['pri']['quesIdList']))
				$flag = 0;
			else
				$flag = 1;
		}
		
		// 返回结果
		$this->checked['ques'][$quesId] = $flag;
		if ($flag == 0)
			return $this->retRes($retType, false);
		else
			return $this->retRes('return', true);
	}
}