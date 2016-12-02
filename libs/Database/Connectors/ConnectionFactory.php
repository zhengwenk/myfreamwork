<?php

namespace Libs\Database\Connectors;

use InvalidArgumentException;
use Libs\Container\Containers;
use Libs\Support\Arr;
use Libs\Database\MySqlConnection;

class ConnectionFactory
{

	protected $container;

	public function __construct(Containers $Container)
	{
		$this->container = $Container;
	}

	public function make(array $config, $name = null)
	{
		$config = $this->parseConfig($config, $name);

		if (isset($config['read'])) {
			return $this->createReadWriteConnection($config);
		}

		return $this->createSingleConnection($config);
	}

	protected function parseConfig(array $config, $name)
	{
		return Arr::add(Arr::add($config, 'prefix', ''), 'name', $name);
	}

	protected function createReadWriteConnection(array $config)
	{
		$connection = $this->createSingleConnection($this->getWriteConfig($config));

		return $connection->setReadPdo($this->createReadPdo($config));
	}

	protected function getWriteConfig(array $config)
	{
		$writeConfig = $this->getReadWriteConfig($config, 'write');

		return $this->mergeReadWriteConfig($config, $writeConfig);
	}

	protected function mergeReadWriteConfig(array $config, array $merge)
	{
		return Arr::except(array_merge($config, $merge), ['read', 'write']);
	}

	protected function getReadWriteConfig(array $config, $type)
	{
		if (isset($config[$type][0])) {
			return $config[$type][array_rand($config[$type])];
		}

		return $config[$type];
	}



	protected function createSingleConnection(array $config)
	{
		$pdo = function () use ($config) {
			return $this->createConnector($config)->connect($config);
		};

		return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
	}

	public function createConnector(array $config)
	{
		if (! isset($config['driver'])) {
			throw new InvalidArgumentException('A driver must be specified.');
		}

		if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
			return $this->container->make($key);
		}

		switch ($config['driver']) {
			case 'mysql':
				return new MySqlConnector;
//			case 'pgsql':
//				return new PostgresConnector;
//			case 'sqlite':
//				return new SQLiteConnector;
//			case 'sqlsrv':
//				return new SqlServerConnector;

		}

		throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
	}

	protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
	{
		if ($this->container->bound($key = "db.connection.{$driver}")) {
			return $this->container->make($key, [$connection, $database, $prefix, $config]);
		}

		switch ($driver) {
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);
//			case 'pgsql':
//				return new PostgresConnection($connection, $database, $prefix, $config);
//			case 'sqlite':
//				return new SQLiteConnection($connection, $database, $prefix, $config);
//			case 'sqlsrv':
//				return new SqlServerConnection($connection, $database, $prefix, $config);

		}

		throw new InvalidArgumentException("Unsupported driver [$driver]");
	}
}