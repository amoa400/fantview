<?php

class CandidateAction extends Action {
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