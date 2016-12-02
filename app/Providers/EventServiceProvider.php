<?php

namespace App\Providers;

use Libs\Support\ServiceProvider;
use Libs\Events\Dispatcher;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = array(
		'App\Events\User\UserRegister' => array('App\Listeners\User\UserRegisterEmail'),
		'App\Events\Sites\SiteCreate' => array('App\Listeners\Sites\SiteCreatePiwik')
	);

	/**
	 * The subscriber classes to register.
	 *
	 * @var array
	 */
	protected $subscribe = [];

	/**
	 * Register the application's event listeners.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher $events
	 * @return void
	 */
	public function boot(Dispatcher $events)
	{
		foreach ($this->listens() as $event => $listeners) {
			foreach ($listeners as $listener) {
				$events->listen($event, $listener);
			}
		}

		foreach ($this->subscribe as $subscriber) {
			$events->subscribe($subscriber);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the events and handlers.
	 *
	 * @return array
	 */
	public function listens()
	{
		return $this->listen;
	}
}
