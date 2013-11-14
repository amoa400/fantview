<?php

class TestModel extends Model {
	// 变量
	public $errorInfo;

	// 字段名称
	public $fieldName = array(
	);
	
	// 数据正确规则
	public $dataRule = array(
		array('name', 'empty'),
		array('name', 'length', array(2, 30)),
		array('duration', 'int'),
		array('desc', 'empty'),
		array('desc', 'length', array(5, 1000)),
		array('cutoff', 'int'),
	);
	
	//数据填充规则
	public $fillRule = array(
		array('user_id', '', 'userId', 1),
		array('name', '一场新的测评', '', 1),
		array('desc', '这是一场新的测评...', '', 1),
		array('duration', '60', '', 1),
		array('answer_type_id', '1', '', 1),
		array('allow_public', '1', '', 1),
		array('need_info', 'name|', '', 1),
		array('allow_lang', 'C|C++|Java|PHP|Python|C#|Javascript|Ruby|Perl|Objective C|', '', 1),
	);

}