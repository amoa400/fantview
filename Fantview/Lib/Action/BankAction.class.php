<?php

// 已加权限

class BankAction extends Action {

	// 列表
	public function index() {
		// 检查权限
		A('Privilege')->isLogin();

		// 获取当前测试有哪些题目
		$filter = array(
			'page' => 'all',
			'test_id' => (int)$_GET['test_id'],
		);
		$const = array(
			'order' => array(
				'field' => 'test_id',
				'type' => 'ASC'
			),
		);
		$testQuesList = D('Common', 'test_question')->rList($filter, $const);
		$testQuesList = $testQuesList['data'];
		// 获取有哪些题目
		$type_id = $_GET['type_id'];
		if (empty($type_id)) $type_id = 4;
		$user_id = $_SESSION['id'];
		if (!empty($_GET['fantview'])) $user_id = 1;
		$filter = array(
			'page' => $_GET['page'],
			'user_id' => $user_id,
			'type_id' => $type_id,
		);
		$const = array(
			'order' => array(
				'field' => 'id',
				'type' => 'ASC'
			),
		);
		$ret = D('Common', 'question')->rList($filter, $const);
		$questionList = $ret['data'];
		foreach($questionList as $key => $question) {
			$questionList[$key] = A('Question')->format($question);
			foreach($testQuesList as $tq) {
				if ($tq['question_id'] == $question['id']) {
					$questionList[$key]['added'] = 1;
					break;
				}
			}
		}

		// 分页信息
		$pager['count'] = $ret['count'];
		$pager['cntPage'] = $_GET['page'];
		if (empty($pager['cntPage'])) $pager['cntPage'] = 1;
		$pager['totPage'] =  ceil($pager['count'] / 10);
		
		// 判断是否页数不对
		if ($pager['cntPage'] < 1)
			$this->ajaxReturn(array('linkUrl' => '/bank/index/page/1'));
		if ($pager['cntPage'] != 1 && $pager['cntPage'] > $pager['totPage'])
			$this->ajaxReturn(array('linkUrl' => '/bank/index/page/' . $pager['totPage']));

		$this->assign('pager', $pager);
		$this->assign('questionList', $questionList);
		$this->assign('type_id', $type_id);
		
		$page['pageTitle'] = '题库列表';
		$page['item1'] = 'question';
		$page['item2'] = 'bank';
		$page['content'] .= $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 添加到测试中
	public function addToTest() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
		
		$ques = D('Common', 'question')->r($_GET['question_id']);
		if ($ques['user_id'] != 1)
			A('Privilege')->haveQues($_GET['question_id']);
		
		A('Question')->createDo($_GET['test_id'], $_GET['question_id']);
	}
	
	// 从测试中删除
	public function deleteFromTest() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_GET['test_id']);
	
		A('Question')->deleteDo($_GET['test_id'], $_GET['question_id']);
	}

	// 删除题目
	public function delete() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);

		// 从各个测试里删除题目
		$quesList = D('Common', 'test_question')->rList(
			array(
				'question_id' => $_GET['question_id'],
				'page' => 'all'
			),
			array(
				'order' => array(
					'field' => 'test_id',
					'type' => 'ASC'
				)
			)
		);
		$quesList = $quesList['data'];
		foreach($quesList as $ques) {
			A('Question')->deleteDo($ques['test_id'], $ques['question_id']);
		}
		
		D('Common', 'question')->d($_GET['question_id']);
		$this->ajaxReturn(array('backward' => '1'));
	}
	
}