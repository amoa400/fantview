<?php

class CommonAction extends Action {

	// 创建完成 TODO DELETE
	public function createDo($data, $para) {
		$dataClass = D('Common', $para['action']);
		$res = $dataClass->c($data);
		// 返回
		if (!empty($dataClass->errorInfo)) {
			$ret['status'] = 'fail';
			if (!empty($dataClass->errorInfo))
				$ret['error'] = $dataClass->errorInfo;
		} else {
			$ret['status'] = 'success';
			if (!empty($para['linkUrl']))
				$ret['linkUrl'] = $para['linkUrl'];
			if (!empty($para['jumpUrl']))
				$ret['jumpUrl'] = $para['jumpUrl'];
			$ret['id'] = $res;
		}
		if ($para['retType'] == 'ajax')
			$this->ajaxReturn($ret);
		else
			return $ret;
	}	
	
	// 编辑完成 TODO DELETE
	public function editDo($data, $para) {
		$dataClass = D('Common', $para['action']);
		$res = $dataClass->u($data);
		// 返回
		if (!empty($dataClass->errorInfo)) {
			$ret['status'] = 'fail';
			if (!empty($dataClass->errorInfo))
				$ret['error'] = $dataClass->errorInfo;
		} else {
			$ret['status'] = 'success';
			if (!empty($para['linkUrl']))
				$ret['linkUrl'] = $para['linkUrl'];
			if (!empty($para['jumpUrl']))
				$ret['jumpUrl'] = $para['jumpUrl'];
		}
		
		if ($para['retType'] == 'ajax')
			$this->ajaxReturn($ret);
		else
			return $ret;
	}
	
	// 获取编号名称对应表
	public function getIdNameList($const) {
		$dataClass = D('Common', $const['action']);
		if (empty($const['order'])) 
			$res = $dataClass->getIdNameList();
		else
			$res = $dataClass->getIdNameList($const['order']);
		// 是否合并
		if (!empty($const['merge'])) {
			$resT = $res;
			$res = array();
			foreach($resT as $item) {
				$res[$item['id']] = $item['name'];
			}
		}
		// 返回方式
		if (!empty($const['noAjax']))
			return $res;
		else
			$this->ajaxReturn($res);
	}
	
	// 获取邮件模板
	public function getMail($title, $username, $cont, $link) {
		$mailCont = $this->fetch('Index:mail');
		$mailCont = str_replace('<{title}>', $title, $mailCont);
		$mailCont = str_replace('<{username}>', $username, $mailCont);
		$mailCont = str_replace('<{cont}>', $cont, $mailCont);
		$mailCont = str_replace('<{link}>', $link, $mailCont);
		return $mailCont;
	}

	// 数据是否正确
	public function isCorrect($dataList, $ruleList) {
		$error = array();
		foreach($dataList as $key => $data) {
			foreach($ruleList as $rule) {
				if ($key != $rule[0]) continue;
				$type = $rule[1];
				$para = $rule[2];
				if (!empty($error[$key])) continue;
				// 空
				if ($type == 'empty') {
					if (empty($data))
						$error[$key] = '不能为空';
				}
				// 长度
				else
				if ($type == 'length') {
					$len = mb_strlen($data, 'utf-8');
					if ($len < $para[0] || $len > $para[1])
						$error[$key] = '长度应为' . $para[0] . '-' . $para[1] . '位';
				}
				// 邮箱
				else
				if ($type == 'email') {
					if (!validEmail($data))
						$error[$key] = '格式错误';
				}
				// 字段是否存在
				else
				if ($type == 'exist') {
					$dataClass = D('Common', $this->action);
					$id = $dataClass->getField($key, $data, 'id');
					if ($id != $data['id'])
						$error[$key] = '已存在';
				}
				else
				// 正则
				if ($type == 'reg') {
					if (!preg_match($para, $data))
						$error[$key] = '格式不正确';
				}
				else
				// 整数
				if ($type == 'int') {
					if (!preg_match('/^[0-9]+$/u', $data))
						$error[$key] = '格式不正确';
				}
				// 空
				else {
				}
			}
		}
		return $error;
	}
	
}