<?php

namespace Libs\Database\Query;

class MySqlProcessor extends Processor
{
	/**
	 * Process the results of a column listing query.
	 *
	 * @param  array  $results
	 * @return array
	 */
	public function processColumnListing($results)
	{
		$mapping = function ($r) {
			$r = (object) $r;

			return $r->column_name;
		};

		return array_map($mapping, $results);
	}
}
