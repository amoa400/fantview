<?php

class CandidateAction extends Action {
	// 下载简历
	public function downloadResume() {
		// 检查权限
		A('Privilege')->isLogin();
		A('Privilege')->haveCd($_GET['id']);

		$cd = D('Common', 'candidate')->r($_GET['id']);

		// 弹出下载
		$file = '../Upload/' . (int)$_SESSION['id']. '/candidate/' . $cd['id'] . '.' . $cd['extension'];
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer"); 
		header("Content-type: application/pdf\n");
		header("Content-disposition: attachment; filename=简历_" . $cd['name'] . ".pdf\n");
		header("Content-transfer-encoding: binary\n");
		header("Content-length: " . @filesize($file) . "\n");
		readfile($file);
	}

	// 格式化
	public function format($cd) {
		$cd['name'] = htmlspecialchars($cd['name']);
		$cd['phone'] = htmlspecialchars($cd['phone']);
		switch($cd['status_id']) {
			case 1:
				$cd['status'] = '已邀请';
				break;
			case 2:
				$cd['status'] = '测评中';
				break;
			case 3:
				$cd['status'] = '已完成';
				break;
			case 4:
				$cd['status'] = '已通过';
				break;
			case 5:
				$cd['status'] = '已淘汰';
				break;
		}
		$cd['invite_time'] = intToTime($cd['invite_time_int'], 'Y-m-d H:i');
		$cd['start_time'] = intToTime($cd['start_time_int'], 'Y-m-d H:i');
		$cd['end_time'] = intToTime($cd['end_time_int'], 'Y-m-d H:i');
		$cd['tot_time'] = ceil($cd['tot_time_int'] / 60);
		return $cd;
	}
}