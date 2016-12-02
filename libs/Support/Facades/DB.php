<?php

namespace Libs\Support\Facades;

/**
 * @see \Libs\Database\DatabaseManager
 * @see \Libs\Database\Connection
 */
class DB extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'db';
	}
}
