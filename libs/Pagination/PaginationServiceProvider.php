<?php

namespace Libs\Pagination;

use Libs\Support\ServiceProvider;

class PaginationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		Paginator::currentPageResolver(function ($pageName, $perPage) {
			$offset = $this->app['request']->get($pageName);

			if (filter_var($offset, FILTER_VALIDATE_INT) !== false && (int) $offset >= 0) {
				return floor($offset/$perPage);
			}

			return 0;
		});
	}
}
