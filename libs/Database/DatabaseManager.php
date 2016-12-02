<?php

namespace Libs\Database;

use Libs\Support\Arr;
use InvalidArgumentException;
use Libs\Database\Connectors\ConnectionFactory;

class DatabaseManager
{
	protected $connections = array();
	protected $extensions = array();
	protected $app;
	protected $factory;

	public function __construct($app, ConnectionFactory $factory)
	{
		$this->app = $app;
		$this->factory = $factory;
	}

	protected function connection($name = null)
	{
		list($name, $type) = $this->parseConnectionName($name);

		if (! isset($this->connections[$name])) {
			$connection = $this->makeConnection($name);

			$this->setPdoForType($connection, $type);

			$this->connections[$name] = $this->prepare($connection);
		}

		return $this->connections[$name];
	}

	protected function setPdoForType(Connection $connection, $type = null)
	{
		if ($type == 'read') {
			$connection->setPdo($connection->getReadPdo());
		} elseif ($type == 'write') {
			$connection->setReadPdo($connection->getPdo());
		}

		return $connection;
	}

	protected function prepare(Connection $connection)
	{
		$connection->setFetchMode($this->app['config']['database.fetch']);

//		if ($this->app->bound('events')) {
//			$connection->setEventDispatcher($this->app['events']);
//		}

		// Here we'll set a reconnector callback. This reconnector can be any callable
		// so we will set a Closure to reconnect from this manager with the name of
		// the connection, which will allow us to reconnect from the connections.
		$connection->setReconnector(function ($connection) {
			$this->reconnect($connection->getName());
		});

		return $connection;
	}

	protected function makeConnection($name)
	{

		$config = $this->getConfig($name);

		if (isset($this->extensions[$name])) {
			return call_user_func($this->extensions[$name], $config, $name);
		}

		$driver = $config['driver'];

		if (isset($this->extensions[$driver])) {
			return call_user_func($this->extensions[$driver], $config, $name);
		}

		return $this->factory->make($config, $name);
	}

	protected function getConfig($name)
	{
		//$name = $name ? $name : $this->getDefaultConnection();
		// To get the database connection configuration, we will just pull each of the
		// connection configurations and get the configurations for the given name.
		// If the configuration doesn't exist, we'll throw an exception and bail.
		$connections = $this->app['config']['database.connections'];

		if (is_null($config = Arr::get($connections, $name))) {
			throw new InvalidArgumentException("Database [$name] not configured.");
		}

		return $config;
	}


	protected function parseConnectionName($name)
	{
		$name = $name ? $name : $this->getDefaultConnection();

		return array($name, null);
	}

	protected function getDefaultConnection()
	{
		return $this->app['config']['database.default'];
	}

	public function __call($method, $parameters)
	{
		return $this->connection()->$method(...$parameters);
	}
}