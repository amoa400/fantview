<?php

class CommonModel extends Model {
	// 变量
	protected $tableName; 
	public $errorInfo;
	public $om;
	public $action;
	
	// 构造函数
	public function _initialize($action) {
		$this->tableName = $action;
		$this->om = D(ucfirst($action));
		$this->action = $action;
	}
	
	// 创建
	public function c($data) {
		if (empty($data)) $data = $_POST;
		$data = $this->fillData($data, 1);
		if (!$this->isCorrect($data, 1)) return false;
		if ($this->create($data)) {
			$ret = $this->add();
			return $ret;
		} else {
			return false;
		}
	}
	
	// 创建多个
	public function cArr($data) {
		$sql = 'INSERT INTO `ftv_' . $this->tableName . '`(';
		foreach($data as $item) {
			$first = true;
			foreach($item as $key => $item2) {
				if ($first)
					$first = false;
				else
					$sql .= ',';
				$sql .= '`' . $key . '`';
			}
			break;
		}
		$sql .= ') VALUES';
		foreach($data as $item) {
			$first = true;
			$sql .= '(';
			foreach($item as $key => $item2) {
				if ($first)
					$first = false;
				else
					$sql .= ',';
				$sql .= '\'' . mysql_real_escape_string($item2) . '\'';
			}
			$sql .= '),';
			
		}
		$sql[strlen($sql) - 1] = ';';
		$ret = $this->query($sql);
		return $ret;
	}
	
	// 修改
	public function u($data) {
		if (empty($data)) $data = $_POST;
		$data = $this->fillData($data, 2);
		if (!$this->isCorrect($data, 2)) return false;
		$ret = $this->save($data);
		return $ret;
	}
	
	// 修改（通过SQL）
	public function uBySql($data, $sql) {
		$ret = $this->where($sql)->save($data);
		return $ret;
	}
	
	
	// 获取
	public function r($id) {
		$sql = array('id' => (int)$id);
		$ret = $this->where($sql)->find();
		return $ret;
	}
	
	// 获取通过SQL
	public function rBySql($sql) {
		$ret = $this->where($sql)->find();
		return $ret;
	}
	
	// 获取指定字段（通过SQL）
	public function rFieldBySql($field, $sql) {
		$ret = $this->field($field)->where($sql)->find();
		return $ret;
	}
	
	// 获取列表
	public function rList($filter, $const) {
		$mysql = $this;
		$sql = array();
		
		// 筛选条件
		foreach($filter as $key => $value) {
			if (empty($value)) continue;
			if ($key == 'page') continue;
			// 特殊条件
			if (!empty($const['special'][$key])) {
				// 时间区间
				if ($const['special'][$key][0] == 'timeBetween') {
					$st = timeToInt($filter[$key]['s']);
					$ed = timeToInt($filter[$key]['e']) + 86399;

					if (!empty($filter[$key]['s']) && !empty($filter[$key]['e']))
						$sql[$key] = array('BETWEEN', array($st, $ed));
					else
					if (!empty($filter[$key]['s'])) 
						$sql[$key] = array('EGT', $st);
					else
					if (!empty($filter[$key]['e'])) 
						$sql[$key] = array('ELT', $ed);
				}
				// 普通区间
				if ($const['special'][$key][0] == 'between') {
					$st = $filter[$key]['s'];
					$ed = $filter[$key]['e'];

					if (!empty($st) && !empty($ed))
						$sql[$key] = array('BETWEEN', array($st, $ed));
					else
					if (!empty($st)) 
						$sql[$key] = array('EGT', $st);
					else
					if (!empty($ed)) 
						$sql[$key] = array('ELT', $ed);
				}
				// 名称选择
				if ($const['special'][$key][0] == 'namePicker') {
					$sql[$key] = $value;
				}
				// 合并查询
				if ($const['special'][$key][0] == 'concat') {
					$s = 'concat(';
					foreach($const['special'][$key][1] as $value2) {
						$s .= '`' . $value2 . '`, \'|\', ';
					}
					$s .= '\'\')';
					$sql['_string'] = $s . ' LIKE \'%'. mysql_real_escape_string($value) . '%\'';
				}
			}
			// 选择框
			else
			if (gettype($value) == 'array') {
				$sql[$key] = array('in', $value);
			}
			// 文本框
			else {
				if (strstr($this->fields['_type'][$key], 'char')) {
					// 模糊匹配
					$sql[$key] = array('LIKE', '%' . $value . '%');
				} else {
					// 精确匹配
					$sql[$key] = $value;
				}
			}
		}
		//dump($sql);
		
		$ret = $mysql->where($sql)->field('COUNT(1) AS `count`')->find();
		if ($filter['page'] != 'all') {
			if (!empty($filter['page'])) $mysql = $mysql->page((int)$filter['page'], 10);
			else $mysql = $mysql->page(1, 10);
		}
		if (empty($const['order']))
			$ret['data'] = $mysql->where($sql)->order('`id` DESC')->select();
		else
			$ret['data'] = $mysql->where($sql)->order('`' . $const['order']['field'] . '` ' . $const['order']['type'])->select();
		
		//dump($this->getLastSql());
		//dump($ret);
		return $ret;
	}
	
	// 获取数量
	public function rCountBySql($sql) {
		$ret = $this->field('COUNT(1) AS `count`')->where($sql)->find();
		return $ret['count'];
	}
	
	// 删除
	public function d($id) {
		$sql = array();
		$sql['id'] = $id;
		$ret = $this->where($sql)->delete();
		return $ret;
	}
	
	// 删除列表
	public function dList($idList) {
		$sql = array();
		$sql['id'] = array('in', $idList);
		$ret = $this->where($sql)->delete();
		return $ret;
	}
	
	// 通过SQL语句删除
	public function dBySql($sql) {
		if (empty($sql)) return;
		$ret = $this->where($sql)->delete();
		return $ret;
	}
	
	// 获取字段
	public function getByField($name, $value) {
		$sql = array();
		$sql[$name] = $value;
		$ret = $this->where($sql)->find();
		return $ret;
	}
	
	// 获取某个字段
	public function getField($name, $value, $target) {
		$sql = array();
		$sql[$name] = $value;
		$ret = $this->field($target)->where($sql)->find();
		return $ret[$target];
	}
	
	// 获取某个字段（通过SQL）
	public function getFieldBySql($sql, $target) {
		$ret = $this->field($target)->where($sql)->find();
		return $ret[$target];
	}

	// 递增某个字段
	public function incField($name, $value, $target, $num = 1) {
		$sql = array();
		$sql[$name] = $value;
		$this->where($sql)->setInc($target, $num);
	}

	// 递减某个字段
	public function decField($name, $value, $target, $num = 1) {
		$sql = array();
		$sql[$name] = $value;
		$this->where($sql)->setDec($target, $num);
	}
	
	// 修改某个字段
	public function changeField($name, $value, $idList) {
		$data[$name] = $value;
		$sql['id'] = array('in', $idList);
		$ret = $this->where($sql)->save($data);
		return $ret;
	}
	
	// 获取编号名称对应表
	public function getIdNameList($order = 'DESC') {
		$ret = $this->field('`id` AS `id`, `name`')->order('`id` ' . $order)->select();
		return $ret;
	}
	
	// 获取所有项
	public function getAll($order = 'DESC') {
		$ret = $this->order('`id` ' . $order)->select();
		return $ret;
	}

	// 验证数据是否合法 TODO DELETE
	public function isCorrect($data, $type = 0) {
		$this->errorInfo = array();
		foreach($this->om->dataRule as $item) {
			$key = $item[0];
			$keyName = $this->om->fieldName[$key];
			if (!empty($this->errorInfo[$key])) continue;
			if (!isInArr($key, $data)) continue;
			// 空
			if ($item[1] == 'empty') {
				if (empty($data[$key]))
					$this->errorInfo[$key] = $keyName . '不能为空';
			}
			// 长度
			else
			if ($item[1] == 'length') {
				$len = mb_strlen($data[$key], 'utf-8');
				if ($len < $item[2][0] || $len > $item[2][1])
					$this->errorInfo[$key] = $keyName . '长度应为' . $item[2][0] . '-' . $item[2][1] . '位';
			}
			// 邮箱
			else
			if ($item[1] == 'email') {
				if (!validEmail($data[$key]))
					$this->errorInfo[$key] = $keyName . '格式错误';
			}
			// 字段是否存在
			else
			if ($item[1] == 'exist') {
				$dataClass = D('Common', $this->action);
				$id = $dataClass->getField($key, $data[$key], 'id');
				if ($id != $data['id'])
					$this->errorInfo[$key] = $keyName . '已存在';
			}
			else
			// 正则
			if ($item[1] == 'reg') {
				if (!preg_match($item[2], $data[$key]))
					$this->errorInfo[$key] = $keyName . '格式不正确';
			}
			else
			// 整数
			if ($item[1] == 'int') {
				if (!preg_match('/^[0-9]+$/u', $data[$key]))
					$this->errorInfo[$key] = $keyName . '格式不正确';
			}
			// 空
			else {
			}
		}
		return empty($this->errorInfo);
	}
	
	// 数据填充 TODO DELETE
	public function fillData($data, $type = 0) {
		foreach($this->om->fillRule as $item) {
			$key = $item[0];
			if (!empty($item[3]) && $type != $item[3]) continue;
			//  直接填充
			if (empty($item[2])) {
				$data[$key] = $item[1];
			}
			// 获取字段
			else
			if ($item[2] == 'getField' && !empty($data[$item[1][1]])) {
				$dataClass = D('Common', $item[1][0]);
				$data[$key] = $dataClass->getField($item[1][1], $data[$item[1][1]], $item[1][2]);
			}
			// 获取当前时间
			else
			if ($item[2] == 'getTime') {
				$data[$key] = getTime();
			}
			// 随机字符串
			else
			if ($item[2] == 'randomChar') {
				if (empty($item[1]))
					$data[$key] = randomChar();
				else
					$data[$key] = randomChar($item[1][0], $item[1][1]);
			}
			// 加密
			else
			if ($item[2] == 'encrypt') {
				if (empty($data[$key])) continue;
				$data[$key] = encrypt($data[$key]);
			}
			// 获取用户编号
			else 
			if ($item[2] == 'userId') {
				$data[$key] = $_SESSION['id'];
			}
			else {
			}
		}
		return $data;
	}
}