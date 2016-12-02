<?php

namespace App\Events\Sites;

class SiteCreate
{
	public $siteid;
	public function __construct($siteid)
	{
		$this->siteid = $siteid;
	}
}
