<?php

namespace Libs\Pagination;

use Closure;
use Libs\Support\Collection;

class Paginator
{
	protected static $currentPageResolver;
	protected $total;
	protected $perPage;
	protected $lastPage;
	protected $currentPage;

	public function __construct($items, $total, $perPage, $currentPage = null, array $options = [])
	{
		foreach ($options as $key => $value) {
			$this->{$key} = $value;
		}

		$this->total = $total;
		$this->perPage = $perPage;
		$this->lastPage = (int) ceil($total / $perPage);
		$this->currentPage = $this->setCurrentPage($currentPage);
		$this->items = $items instanceof Collection ? $items : Collection::make($items);
	}

	public static function resolveCurrentPage($pageName = 'offset', $perPage = 10, $default = 0)
	{
		if (isset(static::$currentPageResolver)) {
			return call_user_func(static::$currentPageResolver, $pageName, $perPage);
		}

		return $default;
	}

	public static function currentPageResolver(Closure $resolver)
	{
		static::$currentPageResolver = $resolver;
	}

	public function setCurrentPage($currentPage)
	{
		$currentPage = $currentPage ?: static::resolveCurrentPage();

		return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
	}

	protected function isValidPageNumber($page)
	{
		return $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false;
	}

	public function toArray()
	{
		return [
			'pageNum' => $this->lastPage(),
			'currPage' => $this->currentPage(),
			'data' => $this->items->toArray(),
		];
	}

	public function total()
	{
		return $this->total;
	}

	public function perPage()
	{
		return $this->perPage;
	}

	public function lastPage()
	{
		return $this->lastPage;
	}

	public function currentPage()
	{
		return $this->currentPage;
	}
}
