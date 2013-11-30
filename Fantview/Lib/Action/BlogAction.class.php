<?php
class BlogAction extends Action {
	// 列表
	public function index() {
		if (!empty($_GET['cateId'])) $cateId = $_GET['cateId'];
		else $cateId = 0;
		if (!empty($_GET['page'])) $page = $_GET['page'];
		else $page = 1;
		$filter = array(
			'page' => $page,
		);
		if ($cateId > 0) $filter['cateId'] = $cateId;
		$blogList = D('Common', 'blog')->rList($filter, array('order' => array('field' => 'id', 'type' => 'ASC')));
		//var_dump($blogList);
		$blogList = $this->format($blogList);
		$this->assign('blogList', $blogList);
		$this->assign('cateId', $cateId);
		$this->assign('page', $page);
		$this->assign('pageTitle', '博客');

		$this->display();
	}

	// 阅读文章
	public function article() {
		$id = $_GET['id'];
		$blog = D('Common', 'blog')->r($id);
		$blog['create_time'] = date('Y-m-d', $blog['create_time_int']);
		$this->assign('blog', $blog);
		$this->assign('cateId', $blog['cateId']);
		$this->assign('pageTitle', $blog['title']);
		$this->display();
	}

	// 格式化
	public function format($blogList) {
		foreach ($blogList['data'] as &$item) {
			$item['create_time'] = date("Y-m-d", $item['create_time_int']);

			$item['desc'] = mb_substr(trim(strip_tags($item['content'])), 0, 150, 'UTF-8').'...';
		}
		return $blogList;
	}
}
