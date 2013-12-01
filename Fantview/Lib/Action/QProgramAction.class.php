<?php

// 已加权限

class QProgramAction extends Action {
	
	// 新建编程题
	public function create() {
		// 检查权限
		A('Privilege')->isLogin();
		if (!empty($_GET['question_id']))
			A('Privilege')->haveQues($_GET['question_id']);
		
		$question = D('Common', 'question')->r($_GET['question_id']);
		$program = D('Common', 'q_program')->r($_GET['question_id']);
		$program = $this->format($program);
		if (empty($question)) {
			$question['score'] = 50;
			$program['time_limit'] = 1000;
			$program['memory_limit'] = 32767;
			$page['item2'] = 'create';
		}
		else {
			$page['item2'] = 'list';
		}
		
		$this->assign('question', $question);
		$this->assign('program', $program);
		
		$page['pageTitle'] = '编程题 - 基本信息';
		$page['item1'] = 'question';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}

	// 新建编程题（处理）
	public function createDo() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveTest($_POST['test_id']);
		if (!empty($_POST['question_id']))
			A('Privilege')->haveQues($_POST['question_id']);
	
		// 问题基本信息
		$data['test_id'] = $_POST['test_id'];
		$data['user_id'] = $_SESSION['id'];
		$data['name'] = mb_substr(htmlspecialchars($_POST['name']), 0, 20, 'utf-8');
		$data['type_id'] = 4;
		$data['score'] = (int)$_POST['score'];
		// 主观问答题信息
		$data2['name'] = $_POST['name'];
		$data2['desc'] = $_POST['desc'];
		$data2['input'] = $_POST['input'];
		$data2['output'] = $_POST['output'];
		$data2['s_input'] = $_POST['s_input'];
		$data2['s_output'] = $_POST['s_output'];
		$data2['hint'] = $_POST['hint'];
		$data2['time_limit'] = (int)$_POST['time_limit'];
		$data2['memory_limit'] = (int)$_POST['memory_limit'];
		
		// 判断正确性
		if (empty($_POST['name']) || mb_strlen($_POST['name'], 'utf-8') > 30)
			$ret['error']['name'] = '长度应为1-30位';
		if (empty($_POST['desc']) || mb_strlen($_POST['desc'], 'utf-8') > 10000)
			$ret['error']['desc_error'] = '长度应为1-10000位';
		if (mb_strlen($_POST['input'], 'utf-8') > 1000)
			$ret['error']['input'] = '长度应为0-1000位';
		if (mb_strlen($_POST['output'], 'utf-8') > 1000)
			$ret['error']['output'] = '长度应为0-1000位';
		if (mb_strlen($_POST['s_input'], 'utf-8') > 1000)
			$ret['error']['s_input'] = '长度应为0-1000位';
		if (mb_strlen($_POST['s_output'], 'utf-8') > 1000)
			$ret['error']['s_output'] = '长度应为0-1000位';
		if (mb_strlen($_POST['hint'], 'utf-8') > 1000)
			$ret['error']['hint'] = '长度应为0-1000位';
		if ($data2['time_limit'] == 0 || $data2['time_limit'] > 10000)
			$ret['error']['time_limit'] = '数值应为1-10000';
		if ($data2['memory_limit'] == 0 || $data2['memory_limit'] > 524288)
			$ret['error']['memory_limit'] = '数值应为1-524288';
		
		// 处理
		if (!empty($ret)) {
			$ret['status'] = 'fail';
		} else {
			$ret['status'] = 'success';
			if (empty($_POST['question_id'])) {
				$res = D('Common', 'question')->c($data);
				$data2['id'] = $res;
				D('Common', 'q_program')->c($data2);
				A('Question')->createDo($_POST['test_id'], $res);
			} else {
				A('Question')->changeScore($_POST['question_id'], $data['score']);
				$data['id'] = $_POST['question_id'];
				D('Common', 'question')->u($data);
				$data2['id'] = $_POST['question_id'];
				D('Common', 'q_program')->u($data2);
			}
			$ret['linkUrl'] = '/q_program/create2/question_id/' . $data2['id'];
		}
		$this->ajaxReturn($ret);
	}
	
	// 新建编程题（测试数据）
	public function create2() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);
			
		$question = D('Common', 'question')->r($_GET['question_id']);
		$program = D('Common', 'q_program')->r($_GET['question_id']);
		$program = $this->format($program);
		if (empty($question)) {
			$question['score'] = 20;
			$program['time_limit'] = 1000;
			$program['memory_limit'] = 32767;
			$page['item2'] = 'create';
		}
		else {
			$page['item2'] = 'list';
		}
		
		$this->assign('question', $question);
		$this->assign('program', $program);
		
		$page['pageTitle'] = '编程题 - 测试数据';
		$page['item1'] = 'question';
		$page['content'] = $this->fetch();
		$this->ajaxReturn($page);
	}
	
	// 新建测试数据
	public function createT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_POST['question_id']);
		
		$program = D('Common', 'q_program')->r($_POST['question_id']);
		$testcase = split('\|', $program['testcase']);
		
		// 创建文件
		mkdir('../Testcase/' . $_POST['question_id'] . '/');
		file_put_contents('../Testcase/' . $_POST['question_id'] . '/' . count($testcase) . '.in', $_POST['input']);
		file_put_contents('../Testcase/' . $_POST['question_id'] . '/' . count($testcase) . '.out', $_POST['output']);
		
		// 写入数据库
		$data['id'] = $_POST['question_id'];
		$data['testcase'] = $program['testcase'] . (int)$_POST['score'] . ',' . filesize('../Testcase/' . $_POST['question_id'] . '/' . count($testcase) . '.in') . ',' . filesize('../Testcase/' . $_POST['question_id'] . '/' . count($testcase) . '.out') . '|';
		D('Common', 'q_program')->u($data);

		$this->ajaxReturn(array('status' => 'success', 'refresh' => 1, 'js' => '$(".new_testcase").modal("hide");$(".modal-backdrop").remove();'));
	}
	
	// 编辑测试数据
	public function editT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_POST['question_id']);
		
		$program = D('Common', 'q_program')->r($_POST['question_id']);
		$testcase = split('\|', $program['testcase']);
		
		// 重新创建文件
		unlink('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.in');
		unlink('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.out');
		file_put_contents('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.in', $_POST['input']);
		file_put_contents('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.out', $_POST['output']);
		
		// 写入数据库
		$data['id'] = $_POST['question_id'];
		$data['testcase'] = '';
		$cnt = 0;
		foreach($testcase as $case) {
			if (empty($case)) continue;
			$cnt++;
			if ($cnt == $_POST['testcase_id']) 
				$data['testcase'] .= (int)$_POST['score'] . ',' . filesize('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.in') . ',' . filesize('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.out') . '|';
			else
				$data['testcase'] .= $case . '|';
		}
		D('Common', 'q_program')->u($data);

		$this->ajaxReturn(array('status' => 'success', 'refresh' => 1, 'js' => '$(".edit_testcase").modal("hide");$(".modal-backdrop").remove();'));
	}
	
	// 获取测试数据
	public function getT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_POST['question_id']);
		
		$data['input'] = file_get_contents('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.in');
		$data['output'] = file_get_contents('../Testcase/' . $_POST['question_id'] . '/' . (int)($_POST['testcase_id']) . '.out');
		$this->ajaxReturn($data);
	}
	
	// 删除测试数据
	public function deleteT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);
		
		$program = D('Common', 'q_program')->r($_GET['question_id']);
		$testcase = split('\|', $program['testcase']);
		$idList = split('\|', $_GET['testcase_id_list']);	
		
		// 删除文件
		$minId = 999999;
		foreach($idList as $id) {
			if (empty($id)) continue;
			unlink('../Testcase/' . $_GET['question_id'] . '/' . (int)($id) . '.in');
			unlink('../Testcase/' . $_GET['question_id'] . '/' . (int)($id) . '.out');
			if ($id < $minId) $minId = (int)$id;
		}
		
		// 重命名文件
		$cnt = $minId;
		for ($i = $minId + 1; $i <= count($testcase) - 1; $i++) {
			if (!file_exists('../Testcase/' . $_GET['question_id'] . '/' . $i . '.in')) continue;
			rename('../Testcase/' . $_GET['question_id'] . '/' . $i . '.in', '../Testcase/' . $_GET['question_id'] . '/' . $cnt . '.in');
			rename('../Testcase/' . $_GET['question_id'] . '/' . $i . '.out', '../Testcase/' . $_GET['question_id'] . '/' . $cnt . '.out');
			$cnt++;
		}
		
		// 写入数据库
		$data['id'] = $_GET['question_id'];
		$data['testcase'] = '';
		$cnt = 0;
		foreach($testcase as $case) {
			if (empty($case)) continue;
			$cnt++;
			if (strstr($_GET['testcase_id_list'], $cnt . '|')) continue;
			$data['testcase'] .= $case . '|';
		}
		D('Common', 'q_program')->u($data);
		$this->ajaxReturn(array('backward' => '1'));
	}
	
	// 上传测试数据
	public function uploadT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_POST['question_id']);
		
		// 复制压缩包
		deldir('../Testcase/temp/' . (int)($_POST['question_id']) . '/');
		mkdir('../Testcase/temp/' . (int)($_POST['question_id']) . '/');
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();
		$upload->maxSize  = 20 * 1024 * 1024;
		$upload->allowExts  = array('zip');
		$upload->savePath =  '../Testcase/temp/' . $_POST['question_id'] . '/';
		if(!$upload->upload())
			$this->ajaxReturn(array('status' => 'fail', 'error' => array('error' => $upload->getErrorMsg())));
		$info = $upload->getUploadFileInfo();
		$info = $info[0];
		// 解压压缩包
		$zip = new ZipArchive;
		if ($zip->open($info['savepath'] . $info['savename'])) {
			$zip->extractTo($info['savepath']);
			$zip->close();
		} else {
			$this->ajaxReturn(array('status' => 'error', 'error' => array('error' => 'zip格式不正确')));
		}
		// 处理每个文件
		$program = D('Common', 'q_program')->r($_POST['question_id']);
		$testcase = split('\|', $program['testcase']);
		$data['id'] = $_POST['question_id'];
		$data['testcase'] = $program['testcase'];
		$cnt = count($testcase) - 1;
			
		$done = array();
		$dp = opendir($info['savepath']);
		while (($file = readdir($dp))) {
			if ($file == '.' || $file == '..') continue;
			// 获取最后一个点的位置
			$dotPositon = -1;
			for ($i = strlen($file) - 1; $i >= 0; $i--)
				if ($file[$i] == '.') {
					$dotPosition = $i;
					break;
				}
			if ($dotPosition == -1) continue;
			// 获取名字和后缀
			$filename = '';
			$extend = '';
			for ($i = 0; $i < $dotPosition; $i++) $filename .= $file[$i];
			for ($i = $dotPosition + 1; $i < strlen($file); $i++) $extend .= $file[$i];
			$extend = strtolower($extend);
			if ($extend != 'in' && $extend != 'out') continue;
			if ($done[$filename] == 1) continue;
			$done[$filename] = 1;
			// 处理
			$cnt++;
			mkdir('../Testcase/' . $_POST['question_id'] . '/');
			$data['testcase'] .= '10,' . filesize($info['savepath'] . $filename . '.in') . ',' . filesize($info['savepath'] . $filename . '.out') . '|';
			copy($info['savepath'] . $filename . '.in', '../Testcase/' . $_POST['question_id'] . '/' . $cnt . '.in');
			copy($info['savepath'] . $filename . '.out', '../Testcase/' . $_POST['question_id'] . '/' . $cnt . '.out');
		}
		deldir('../Testcase/temp/' . (int)($_POST['question_id']) . '/');
		// 写入数据库
		D('Common', 'q_program')->u($data);
		$this->ajaxReturn(array('status' => 'success', 'refresh' => 1, 'js' => '$(".upload_testcase").modal("hide");$(".modal-backdrop").remove();'));
	}
	
	// 下载测试数据
	public function downloadT() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);
		
		$idList = split('\|', $_GET['testcase_id_list']);
		// 压缩测试数据
		mkdir('../Testcase/temp/' . (int)($_GET['question_id']) . '/');
		$zip = new ZipArchive();
		$zip->open('../Testcase/temp/' . $_GET['question_id'] . '/' . $_GET['question_id'] . '_testcase.zip', ZipArchive::OVERWRITE);
		foreach($idList as $id) {
			if (empty($id)) continue;
			$zip->addFile('../Testcase/' . $_GET['question_id'] . '/' . $id . '.in', $id . '.in');
			$zip->addFile('../Testcase/' . $_GET['question_id'] . '/' . $id . '.out', $id . '.out');
		}
		$zip->close();
		// 下载数据
		vendor('Download.download');
		$dw = new download('../Testcase/temp/' . $_GET['question_id'] . '/' . $_GET['question_id'] . '_testcase.zip', $_GET['question_id'] . '_testcase.zip');
		$dw->getfiles();
		unlink('../Testcase/temp/' . $_GET['question_id'] . '/' . $_GET['question_id'] . '_testcase.zip');
	}
	
	// 改变分数
	public function changeTScore() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveQues($_GET['question_id']);
		
		$program = D('Common', 'q_program')->r($_GET['question_id']);
		$testcase = split('\|', $program['testcase']);
		
		$data['id'] = $_GET['question_id'];
		$data['testcase'] = '';
		$cnt = 0;
		foreach($testcase as $case) {
			if (empty($case)) continue;
			$cnt++;
			if (strstr($_GET['testcase_id_list'], $cnt . '|')) {
				$detail = split(',', $case);
				$data['testcase'] .= (int)$_GET['score'] . ',' . $detail[1] . ',' . $detail[2] . '|';
			} else 
				$data['testcase'] .= $case . '|';
		}
		// 写入数据库
		D('Common', 'q_program')->u($data);
		$this->ajaxReturn(array('status' => 'success', 'refresh' => 1, 'js' => '$(".change_score").modal("hide");$(".modal-backdrop").remove();'));
	}

	// 格式化编程题
	public function format($program) {
		$program['inputStr'] = nl2br($program['input']);
		$program['outputStr'] = nl2br($program['output']);
		$program['s_inputStr'] = nl2br($program['s_input']);
		$program['s_outputStr'] = nl2br($program['s_output']);
		$program['hintStr'] = nl2br($program['hint']);
		$testcases = split('\|', $program['testcase']);
		foreach($testcases as $case) {
			$detail = split(',', $case);
			$data['score'] = $detail[0];
			$data['inputSize'] = $detail[1];
			$data['outputSize'] = $detail[2];
			if ($data['inputSize'] > 1024 * 1024)
				$data['inputSize2'] = round($data['inputSize'] / 1024 / 1024, 2) . ' MB';
			else
			if ($data['inputSize'] > 1024)
				$data['inputSize2'] = round($data['inputSize'] / 1024, 2) . ' KB';
			else
				$data['inputSize2'] = $data['inputSize'] . ' Bytes';
			if ($data['outputSize'] > 1024 * 1024)
				$data['outputSize2'] = round($data['outputSize'] / 1024 / 1024, 2) . ' MB';
			else
			if ($data['outputSize'] > 1024)
				$data['outputSize2'] = round($data['outputSize'] / 1024, 2) . ' KB';
			else
				$data['outputSize2'] = $data['outputSize'] . ' Bytes';

			$program['testcases'][] = $data;
		}
		unset($program['testcases'][count($program['testcases']) - 1]);
		return $program;
	}

	// 格式化答案
	public  function formatAns($ans) {
		switch($ans['lang']) {
			case 1:
				$ans['langStr'] = 'C';
				$ans['langStr2'] = 'cpp';
				break;
			case 2:
				$ans['langStr'] = 'C++';
				$ans['langStr2'] = 'cpp';
				break;
			case 3:
				$ans['langStr'] = 'Pascal';
				$ans['langStr2'] = 'delphi';
				break;
			default:
				$ans['langStr'] = '未知';
				$ans['langStr2'] = '';
		}
		// 运行结果
		if ($ans['result'][0] == 'C' && $ans['result'][1] == 'E' && $ans['result'][2] == ',') {
			$ans['error'] = nl2br(trim(substr($ans['result'], 3)));
		} else {
			$ans['res'] = array();
			$res = split('\|', $ans['result']);
			foreach($res as $item) {
				if (empty($item)) continue;
				$ans['res'][] = array();
				$id = count($ans['res']) - 1;
				$tmp = split(',', $item);
				$ans['res'][$id]['status_id'] = $tmp[0];
				$ans['res'][$id]['time'] = $tmp[1];
				$ans['res'][$id]['memory'] = $tmp[2];
				$ans['res'][$id]['status'] = $tmp[3];
			}
		}
		return $ans;
	}
}