<?php

class UserModel extends Model {
	// 变量
	public $errorInfo;

	// 字段名称
	public $fieldName = array(
		'id' 					=> 		'编号',
		'name' 					=> 		'姓名',
		'email' 				=> 		'邮箱',
		'password' 				=> 		'密码',
	);
	
	// 数据正确规则
	public $dataRule = array(
		array('email', 'empty'),
		array('email', 'length', array(2, 40)),
		array('email', 'email'),
		array('email', 'exist'),
		array('password', 'empty'),
		array('name', 'empty'),
		array('name', 'length', array(2, 20)),
		array('name', 'reg', '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_-]+$/u'),
	);
}