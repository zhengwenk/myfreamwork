<?php
namespace Libs\Events;

use Libs\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('events', function ($app) {
//			return (new Dispatcher($app))->setQueueResolver(function () use ($app) {
//				return $app->make('Illuminate\Contracts\Queue\Factory');
//			});
			return (new Dispatcher($app));
		});
	}
}