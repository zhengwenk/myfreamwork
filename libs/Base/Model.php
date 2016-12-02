<?php

namespace Libs\Base;

use DB;
use Exception;

class Model
{
	protected $table;
	//protected $db;

	public function __construct($debug = false)
	{
		//$this->db = DB::table($this->table);
		if ($debug) {
			DB::enableQueryLog();
		}

//		if (!$this->table) {
//			throw new Exception('Model->__construct: $table is null');
//		}
	}

	public function getDB($table = null)
	{
		return $table ? DB::table($table) : DB::table($this->table);
	}

	public function getLog()
	{
		var_dump(DB::getQueryLog());
	}

	public function Compare($input, $dbData, $field)
	{
		if (is_array($dbData) && key_exists($field, $dbData)) {
			return $input == $dbData[$field] ? true :false;
		} else {
			return false;
		}
	}
}
