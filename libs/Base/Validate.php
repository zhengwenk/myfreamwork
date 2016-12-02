<?php

namespace Libs\Base;

use GUMP;

class Validate extends GUMP
{

	public static function get_instance()
	{
		if(static::$instance === null)
		{
			static::$instance = new static();
		}
		return static::$instance;
	}

	public static function is_valid(array $data, array $validators)
	{
		$gump = static::get_instance();

		$gump->validation_rules($validators);

		if ($gump->run($data) === false) {
			return $gump->get_readable_errors(false);
		} else {
			return true;
		}
	}

	public static function add_validator($rule, $callback)
	{
		$method = 'validate_'.$rule;

		if (method_exists(__CLASS__, $method) || isset(static::$validation_methods[$rule])) {
			throw new \Exception("Validator rule '$rule' already exists.");
		}

		static::$validation_methods[$rule] = $callback;

		return true;
	}

	/**
	 * @param $field
	 * @param $input
	 * @param null $param
	 * @return mixed
	 */
	protected function validate_password($field, $input, $param = null)
	{
		if (!isset($input[$field]) || empty($input[$field])) {
			return;
		}

		if (!preg_match('/[\w\W]{6,32}$/i', $input[$field]) !== false) {
			return array(
				'field' => $field,
				'value' => $input[$field],
				'rule' => __FUNCTION__,
				'param' => $param,
			);
		}
	}

	public function get_readable_errors($convert_to_string = false, $field_class = 'gump-field', $error_class = 'gump-error-message')
	{
		if (empty($this->errors)) {
			return ($convert_to_string) ? null : array();
		}

		$resp = array();

		foreach ($this->errors as $e) {
			$field = ucwords(str_replace($this->fieldCharsToRemove, chr(32), $e['field']));
			$param = $e['param'];

			// Let's fetch explicit field names if they exist
			if (array_key_exists($e['field'], self::$fields)) {
				$field = self::$fields[$e['field']];
			}

			switch ($e['rule']) {
				case 'mismatch' :
					$resp[] = "There is no validation rule for $field_class $field";
					break;
				case 'validate_required' :
					$resp[] = "The $field field is required";
					break;
				case 'validate_valid_email':
					$resp[] = "The $field field is required to be a valid email address";
					break;
				case 'validate_max_len':
					$resp[] = "The $field field needs to be $param or shorter in length";
					break;
				case 'validate_min_len':
					$resp[] = "The $field field needs to be $param or longer in length";
					break;
				case 'validate_exact_len':
					$resp[] = "The $field field needs to be exactly $param characters in length";
					break;
				case 'validate_alpha':
					$resp[] = "The $field field may only contain alpha characters(a-z)";
					break;
				case 'validate_alpha_numeric':
					$resp[] = "The $field field may only contain alpha-numeric characters";
					break;
				case 'validate_alpha_dash':
					$resp[] = "The $field field may only contain alpha characters &amp; dashes";
					break;
				case 'validate_numeric':
					$resp[] = "The $field field may only contain numeric characters";
					break;
				case 'validate_integer':
					$resp[] = "The $field field may only contain a numeric value";
					break;
				case 'validate_boolean':
					$resp[] = "The $field field may only contain a true or false value";
					break;
				case 'validate_float':
					$resp[] = "The $field field may only contain a float value";
					break;
				case 'validate_valid_url':
					$resp[] = "The $field field is required to be a valid URL";
					break;
				case 'validate_url_exists':
					$resp[] = "The $field URL does not exist";
					break;
				case 'validate_valid_ip':
					$resp[] = "The $field field needs to contain a valid IP address";
					break;
				case 'validate_valid_cc':
					$resp[] = "The $field field needs to contain a valid credit card number";
					break;
				case 'validate_valid_name':
					$resp[] = "The $field field needs to contain a valid human name";
					break;
				case 'validate_contains':
					$resp[] = "The $field field needs to contain one of these values: ".implode(', ', $param);
					break;
				case 'validate_contains_list':
					$resp[] = "The $field field needs to contain a value from its drop down list";
					break;
				case 'validate_doesnt_contain_list':
					$resp[] = "The $field field contains a value that is not accepted";
					break;
				case 'validate_street_address':
					$resp[] = "The $field field needs to be a valid street address";
					break;
				case 'validate_date':
					$resp[] = "The $field field needs to be a valid date";
					break;
				case 'validate_min_numeric':
					$resp[] = "The $field field needs to be a numeric value, equal to, or higher than $param";
					break;
				case 'validate_max_numeric':
					$resp[] = "The $field field needs to be a numeric value, equal to, or lower than $param";
					break;
				case 'validate_starts':
					$resp[] = "The $field field needs to start with $param";
					break;
				case 'validate_extension':
					$resp[] = "The $field field can have the following extensions $param";
					break;
				case 'validate_required_file':
					$resp[] = "The $field field is required";
					break;
				case 'validate_equalsfield':
					$resp[] = "The $field field does not equal $param field";
					break;
				case 'validate_min_age':
					$resp[] = "The $field field needs to have an age greater than or equal to $param";
					break;
				default:
					$resp[] = "The $field field is invalid";
			}
		}

		if (!$convert_to_string) {
			return $resp;
		} else {
			$buffer = '';
			foreach ($resp as $s) {
				$buffer .= "<span class=\"$error_class\">$s</span>";
			}

			return $buffer;
		}
	}
}
