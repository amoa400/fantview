<?php

// 已加权限

class QBaseAction extends Action {

	// 新建单项选择题
	public function createSingle() {
		// 检查权限
		A('Privilege')->isLogin();
		if (!empty($_GET['question_id']))
			A('Privilege')->haveQues($_GET['question_id']);
		
		$question = D('Common', 'question')->r($_GET['question_id']);
		$single = D('Common', 'q_single')->r($_GET['question_id']);
		$single = $this->formatSingle($single);
		if (empty($question)) {
			$question['score'] = 10;
			$page['item2'] = 'create';
		}
		else {
			$page['item2'] = 'list';
		}
		
		$this->assign('question', $question);
		$this->assign('single', $single);
		
		$page['pageTitle'] = '单项选择题';
		$page['item1'] = 'question';
		$page['content'] = $this->fetch('createSingle');
		$this->ajaxReturn($page);
	}
	
	// 新建单项选择题（处理）
	public function createSingleDo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
		if (!empty($_POST['question_id']))
			A('Privilege')->haveQues($_POST['question_id']);
		
		// 问题基本信息
		$data['user_id'] = $_SESSION['id'];
		$data['name'] = mb_substr(strip_tags($_POST['desc']), 0, 20, 'utf-8');
		$data['type_id'] = 1;
		$data['score'] = (int)$_POST['score'];
		// 单选题信息
		$data2['desc'] = $_POST['desc'];
		$cnt = 'A';
		$data2['option'] = '';
		for ($i = 'A'; $i <= 'Z'; $i++) {
			if (empty($_POST['option_' . $i])) continue;
			$data2['option'] .= $_POST['option_' . $i] . '|-|$|';
			if ($i == $_POST['answer']) $data2['answer'] = $cnt;
			$cnt++;
		}
		
		// 判断正确性
		if (empty($_POST['desc']) || mb_strlen($_POST['desc'], 'utf-8') > 1000)
			$ret['error']['desc_error'] = '长度应为1-1000位';
		if (empty($data2['answer']))
			$ret['error']['option_A'] = '选项信息不正确';
			
		// 处理
		if (!empty($ret)) {
			$ret['status'] = 'fail';
		} else {
			$ret['status'] = 'success';
			if (empty($_POST['question_id'])) {
				$res = D('Common', 'question')->c($data);
				$data2['id'] = $res;
				D('Common', 'q_single')->c($data2);
				A('Question')->createDo($_POST['test_id'], $res);
			} else {
				$data['id'] = $_POST['question_id'];
				D('Common', 'question')->u($data);
				$data2['id'] = $_POST['question_id'];
				D('Common', 'q_single')->u($data2);
			}
			$ret['linkUrl'] = '/question/index/page/' . ceil(D('Common', 'test_question')->getFieldBySql(array('test_id' => $_POST['test_id'], 'question_id' => $data2['id']), 'rank') / 10);
		}
		$this->ajaxReturn($ret);
	}
	
	// 新建不定项选择题
	public function createMulti() {
		// 检查权限
		A('Privilege')->isLogin();
		if (!empty($_GET['question_id']))
			A('Privilege')->haveQues($_GET['question_id']);

		$question = D('Common', 'question')->r($_GET['question_id']);
		$multi = D('Common', 'q_multi')->r($_GET['question_id']);
		$multi = $this->formatMulti($multi);
		if (empty($question)) {
			$question['score'] = 10;
			$page['item2'] = 'create';
		}
		else {
			$page['item2'] = 'list';
		}
		
		$this->assign('question', $question);
		$this->assign('multi', $multi);
		
		$page['pageTitle'] = '不定项选择题';
		$page['item1'] = 'question';
		$page['content'] = $this->fetch('createMulti');
		$this->ajaxReturn($page);
	}
	
	// 新建不定项选择题（处理）
	public function createMultiDo() {	
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
		if (!empty($_POST['question_id']))
			A('Privilege')->haveQues($_POST['question_id']);

		// 问题基本信息
		$data['user_id'] = $_SESSION['id'];
		$data['name'] = mb_substr(strip_tags($_POST['desc']), 0, 20, 'utf-8');
		$data['type_id'] = 2;
		$data['score'] = (int)$_POST['score'];
		// 不定项选择题信息
		$data2['desc'] = $_POST['desc'];
		$cnt = 'A';
		$data2['option'] = '';
		for ($i = 'A'; $i <= 'Z'; $i++) {
			if (empty($_POST['option_' . $i])) continue;
			$data2['option'] .= $_POST['option_' . $i] . '|-|$|';
			if (strstr($_POST['answer'], $i)) $data2['answer'] .= $cnt;
			$cnt++;
		}
		
		// 判断正确性
		if (empty($_POST['desc']) || mb_strlen($_POST['desc'], 'utf-8') > 1000)
			$ret['error']['desc_error'] = '长度应为1-1000位';
		if (empty($data2['answer']))
			$ret['error']['option_A'] = '选项信息不正确';
			
			
		// 处理
		if (!empty($ret)) {
			$ret['status'] = 'fail';
		} else {
			$ret['status'] = 'success';
			if (empty($_POST['question_id'])) {
				$res = D('Common', 'question')->c($data);
				$data2['id'] = $res;
				D('Common', 'q_multi')->c($data2);
				A('Question')->createDo($_POST['test_id'], $res);
			} else {
				$data['id'] = $_POST['question_id'];
				D('Common', 'question')->u($data);
				$data2['id'] = $_POST['question_id'];
				D('Common', 'q_multi')->u($data2);
			}
			$ret['linkUrl'] = '/question/index/page/' . ceil(D('Common', 'test_question')->getFieldBySql(array('test_id' => $_POST['test_id'], 'question_id' => $data2['id']), 'rank') / 10);
		}
		$this->ajaxReturn($ret);
	}
	
	// 新建主观问答题
	public function createQA() {
		// 检查权限
		A('Privilege')->isLogin();
		if (!empty($_GET['question_id']))
			A('Privilege')->haveQues($_GET['question_id']);
			
		$question = D('Common', 'question')->r($_GET['question_id']);
		$qa = D('Common', 'q_qa')->r($_GET['question_id']);
		$qa = $this->formatQA($qa);
		if (empty($question)) {
			$question['score'] = 10;
			$page['item2'] = 'create';
		}
		else {
			$page['item2'] = 'list';
		}
		
		$this->assign('question', $question);
		$this->assign('qa', $qa);
		
		$page['pageTitle'] = '主观问答题';
		$page['item1'] = 'question';
		$page['content'] = $this->fetch('createQA');
		$this->ajaxReturn($page);
	}
	
	// 新建主观问答题（处理）
	public function createQADo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
		if (!empty($_POST['question_id']))
			A('Privilege')->haveQues($_POST['question_id']);

		// 问题基本信息
		$data['test_id'] = $_POST['test_id'];
		$data['user_id'] = $_SESSION['id'];
		$data['name'] = mb_substr(strip_tags($_POST['desc']), 0, 20, 'utf-8');
		$data['type_id'] = 3;
		$data['score'] = (int)$_POST['score'];
		// 主观问答题信息
		$data2['desc'] = $_POST['desc'];
		
		// 判断正确性
		if (empty($_POST['desc']) || mb_strlen($_POST['desc'], 'utf-8') > 3000)
			$ret['error']['desc_error'] = '长度应为1-3000位';
			
		// 处理
		if (!empty($ret)) {
			$ret['status'] = 'fail';
		} else {
			$ret['status'] = 'success';
			if (empty($_POST['question_id'])) {
				$res = D('Common', 'question')->c($data);
				$data2['id'] = $res;
				D('Common', 'q_qa')->c($data2);
				A('Question')->createDo($_POST['test_id'], $res);
			} else {
				$data['id'] = $_POST['question_id'];
				D('Common', 'question')->u($data);
				$data2['id'] = $_POST['question_id'];
				D('Common', 'q_qa')->u($data2);
			}
			$ret['linkUrl'] = '/question/index/page/' . ceil(D('Common', 'test_question')->getFieldBySql(array('test_id' => $_POST['test_id'], 'question_id' => $data2['id']), 'rank') / 10);
		}
		$this->ajaxReturn($ret);
	}

	// 格式化单选题
	public function formatSingle($single) {
		$single['options'] = split('\|-\|\$\|', $single['option']);
		unset($single['options'][count($single['options']) - 1]);
		return $single;
	}
	
	// 格式化不定项选择题
	public function formatMulti($multi) {
		$multi['options'] = split('\|-\|\$\|', $multi['option']);
		unset($multi['options'][count($multi['options']) - 1]);
		return $multi;
	}
	
	// 格式化主观问答题
	public function formatQA($qa) {
		return $qa;
	}

}