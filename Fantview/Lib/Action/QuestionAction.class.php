<?php

// 已加权限

class QuestionAction extends Action {
	// 列表
	public function index() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
		
		// 获取有哪些题目
		$filter = array(
			'page' => 'all',
			'test_id' => (int)$_GET['test_id']
		);
		$const = array(
			'order' => array(
				'field' => 'rank',
				'type' => 'ASC'
			),
		);
		$ret = D('Common', 'test_question')->rList($filter, $const);
		// 获取题目列表
		$idList = array();
		foreach($ret['data'] as $item) $idList[] = $item['question_id'];
		$ret2 = D('Common', 'question')->rList(array('id' => $idList, 'page' => 'all'), array());
		$questionList = array();
		foreach($idList as $id) {
			foreach($ret2['data'] as $ques) {
				if ($id == $ques['id']) {
					$questionList[] = $ques;
					break;
				}
			}
		}
		foreach($questionList as $key => $question) {
			$questionList[$key] = $this->format($question);
		}

		/*
		// 分页信息
		$pager['count'] = $ret['count'];
		$pager['cntPage'] = $_GET['page'];
		if (empty($pager['cntPage'])) $pager['cntPage'] = 1;
		$pager['totPage'] =  ceil($pager['count'] / 10);
		
		// 判断是否页数不对
		if ($pager['cntPage'] < 1)
			$this->ajaxReturn(array('linkUrl' => '/question/index/page/1'));
		if ($pager['cntPage'] != 1 && $pager['cntPage'] > $pager['totPage'])
			$this->ajaxReturn(array('linkUrl' => '/question/index/page/' . $pager['totPage']));
		*/

		$this->assign('pager', $pager);
		$this->assign('questionList', $questionList);
		
		$page['pageTitle'] = '题目列表';
		$page['item1'] = 'question';
		$page['item2'] = 'list';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 新建
	public function create() {
		// 检查权限
		A('Privilege')->isLogin();

		$page['pageTitle'] = '添加题目';
		$page['item1'] = 'question';
		$page['item2'] = 'create';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 新建（处理）
	public function createDo($test_id, $question_id) {	
		D('Common', 'test_question')->c(array(
			'test_id' => $test_id, 
			'question_id' => $question_id,
			'rank' => (D('Common', 'test')->getField('id', $test_id, 'count_question') + 1)
		));
		D('Common', 'test')->incField('id', $test_id, 'count_question');
		$ques = D('Common', 'question')->r($question_id);
		D('Common', 'test')->incField('id', $test_id, 'tot_score', $ques['score']);
	}
	
	// 编辑
	public function edit() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);

		$question = D('Common', 'question')->r($_GET['question_id']);
		if ($question['type_id'] == 1)
			$ret['linkUrl'] = '/q_base/createSingle/question_id/' . $question['id'];
		if ($question['type_id'] == 2)
			$ret['linkUrl'] = '/q_base/createMulti/question_id/' . $question['id'];
		if ($question['type_id'] == 3)
			$ret['linkUrl'] = '/q_base/createQA/question_id/' . $question['id'];
		if ($question['type_id'] == 4)
			$ret['linkUrl'] = '/q_program/create/question_id/' . $question['id'];
		$this->ajaxReturn($ret);
	}
	
	// 删除
	public function delete() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
		A('Privilege')->haveQues($_GET['question_id']);

		$this->deleteDo($_GET['test_id'], $_GET['question_id']);
		$this->ajaxReturn(array('backward' => '1'));
	}
	
	// 删除（处理）
	public function deleteDo($test_id, $question_id) {
		$tq = D('Common', 'test_question')->rBySql(array(
			'test_id' => $test_id,
			'question_id' => $question_id
		));
		if (empty($tq)) return;
		D('Common', 'test_question')->dBySql(array(
			'test_id' => $test_id,
			'question_id' => $question_id
		));
		D('Common', 'test')->decField('id', $test_id, 'count_question');
		D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => array('GT', $tq['rank'])))->setDec('rank');
		$ques = D('Common', 'question')->r($question_id);
		D('Common', 'test')->decField('id', $test_id, 'tot_score', $ques['score']);
	}

	// 重新排序
	public function reSetId() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
		
		$st = (int)$_GET['st'];
		$ed = (int)$_GET['ed'];
		$test_id = (int)$_GET['test_id'];
		if ($st < $ed) {
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => $st))->save(array('rank' => 0));
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => array('BETWEEN', array($st + 1, $ed))))->setDec('rank');
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => 0))->save(array('rank' => $ed));
		} else {
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => $st))->save(array('rank' => 0));
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => array('BETWEEN', array($ed, $st - 1))))->setInc('rank');
			D('Common', 'test_question')->where(array('test_id' => $test_id, 'rank' => 0))->save(array('rank' => $ed));
		}
	}
	
	// 获取题目详情
	public function getDetail() {
		// 检查权限
		A('Privilege')->isLogin();
		
		$ques = D('Common', 'question')->r($_GET['id']);
		if ($ques['user_id'] != 1)
			A('Privilege')->haveQues($_GET['id']);
		
		$ques = D('Common', 'question')->r($_GET['id']);
		$ques = $this->format($ques);
		if ($ques['type_id'] == 1)
			$ques['detail'] = A('QBase')->formatSingle(D('Common', 'q_single')->r($_GET['id']));
		if ($ques['type_id'] == 2)
			$ques['detail'] = A('QBase')->formatMulti(D('Common', 'q_multi')->r($_GET['id']));
		if ($ques['type_id'] == 3)
			$ques['detail'] = A('QBase')->formatQA(D('Common', 'q_qa')->r($_GET['id']));
		if ($ques['type_id'] == 4)
			$ques['detail'] = A('QProgram')->format(D('Common', 'q_program')->r($_GET['id']));
		
		$this->ajaxReturn($ques);
	}
	
	// 格式化
	public function format($question) {
		switch ($question['type_id']) {
			case 1:
				$question['type'] = '单项选择题';
				break;
			case 2:
				$question['type'] = '不定项选择题';
				break;
			case 3:
				$question['type'] = '自主问答题';
				break;
			case 4:
				$question['type'] = '编程题';
				break;
		}
		return $question;
	}

}