<?php

namespace Libs\Support;

class ServiceProvider
{
	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}
}