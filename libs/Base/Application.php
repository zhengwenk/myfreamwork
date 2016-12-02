<?php

namespace Libs\Base;


use Closure;
use Exception;
use FastRoute\Dispatcher;
use Libs\Container\Containers;
use Libs\Support\Facades\Facade;
use Libs\Support\ServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Libs\Http\Exception\HttpResponseException;
use Libs\Config\Repository as Config;
use Symfony\Component\HttpFoundation\Request as symfonyRequest;
use Symfony\Component\HttpFoundation\Response as symfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Application extends Containers
{
	use RegistersExceptionHandlers;

	protected static $aliasesRegistered = false;
	public $basePath = '';
	public $namedRoutes = array();
	protected $env;
	protected $aliases = array();
	protected $loadedProviders = array();
	protected $loadedConfigurations = array();
	protected $ranServiceBinders = array();
	protected $dispatcher;
	protected $groupAttributes;
	protected $routes = array();
	protected $currRoute = array();

	public $availableBindings = array(
		'config' => 'registerConfigBindings',
		'db' => 'registerDatabaseBindings',
		'request' => 'registerRequestBindings',
		'Symfony\Component\HttpFoundation\Request' => 'registerRequestBindings',
		'response' => 'registerResponseBindings',
		'Symfony\Component\HttpFoundation\Response' => 'registerResponseBindings',
		'validate' => 'registerValidateBindings',
		'Libs\Base\Validate' => 'registerValidateBindings',
		'encrypter' => 'registerEncrypterBindings',
		'Libs\Encryption\Encrypter' => 'registerEncrypterBindings',
		'events' => 'registerEventBindings',
		'mailer' => 'registerMailerBindings',
		'logger' => 'registerLoggerBindings'
	);

	public function __construct($basePath = null)
	{
		date_default_timezone_set(env('APP_TIMEZONE', 'PRC'));
		$this->env = $this->environment();
		$this->basePath = $basePath;
		$this->bootstrapContainer();
		$this->registerErrorHandling();
	}

	protected function bootstrapContainer()
	{
		static::setInstance($this);

		$this->instance('app', $this);
		$this->instance('Libs\Base\Application', $this);
		$this->instance('path', $this->path());
		$this->instance('env', $this->environment());

		$this->registerContainerAliases();
	}

	protected function environment()
	{
		return env('APP_ENV', 'dev');
	}

	protected function registerContainerAliases()
	{
		$this->aliases = [
			'Libs\Config\Repository' => 'config',
			'Libs\Database\DatabaseManager' => 'db',
			'Libs\Base\Validate' => 'validate',
			'Symfony\Component\HttpFoundation\Request' => 'request',
			'Symfony\Component\HttpFoundation\Response' => 'response',
			'Libs\Encryption\Encrypter' => 'encrypter',
			'Libs\Events\Dispatcher' => 'events',
			'Libs\Mail\Mailer' => 'mailer'
		];
	}

	protected function registerRequestBindings()
	{
		$this->singleton('request', function() {
			return symfonyRequest::createFromGlobals();
		});
	}

	protected function registerResponseBindings()
	{
		$this->singleton('response', function() {
			return new symfonyResponse();
		});
	}

	protected function registerValidateBindings()
	{
		$this->singleton('validate', function() {
			return new Validate();
		});
	}

	protected function registerDatabaseBindings()
	{
		$this->singleton('db', function () {
			return $this->loadComponent(
				'database', [
				'Libs\Database\DatabaseServiceProvider',
				'Libs\Pagination\PaginationServiceProvider'
			], 'db'
			);
		});
	}

	protected function registerLoggerBindings()
	{
		$this->singleton('logger', function () {
			return new Logger('larkcn', [$this->getMonologHandler()]);
		});
	}

	protected function registerEncrypterBindings()
	{
		$this->singleton('encrypter', function () {
			return $this->loadComponent(
				'app', [
				'Libs\Encryption\EncryptionServiceProvider',
			], 'encrypter'
			);
		});
	}

	protected function registerEventBindings()
	{
		$this->singleton('events', function(){
			$this->register('Libs\Events\EventServiceProvider');
			return $this->make('events');
		});
	}

	protected function registerMailerBindings()
	{
		$this->singleton('mailer', function () {
			return $this->loadComponent(
				'mail',
				'Libs\Mail\MailServiceProvider',
				'mailer'
			);
		});
	}

	protected function registerConfigBindings()
	{
		$this->singleton('config', function () {
			return new Config;
		});
	}


	public function loadComponent($config, $providers, $return = null)
	{
		$this->configure($config);

		foreach ((array) $providers as $provider) {
			$this->register($provider);
		}

		return $this->make($return ?: $config);
	}

	public function register($provider, $options = [], $force = false)
	{
		if (! $provider instanceof ServiceProvider) {
			$provider = new $provider($this);
		}

		if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
			return true;
		}

		$this->loadedProviders[$providerName] = true;

		if (method_exists($provider, 'register')) {
			$provider->register();
		}

		if (method_exists($provider, 'boot')) {
			return $this->call([$provider, 'boot']);
		}
		return true;
	}

	public function configure($name)
	{
		if (isset($this->loadedConfigurations[$name])) {
			return;
		}

		$this->loadedConfigurations[$name] = true;

		$path = $this->getConfigurationPath($name);

		if ($path) {
			$this->make('config')->set($name, require $path);
		}
	}

	public function getConfigurationPath($name = null)
	{
		if (! $name) {
			$appConfigDir = $this->basePath('config').'/';

			if (file_exists($appConfigDir)) {
				return $appConfigDir;
			} elseif (file_exists($path = __DIR__.'/../config/')) {
				return $path;
			}
		} else {
			$appConfigPath = $this->basePath('config').'/'.$name.'.php';

			if (file_exists($appConfigPath)) {
				return $appConfigPath;
			} elseif (file_exists($path = __DIR__.'/../config/'.$name.'.php')) {
				return $path;
			}
		}
	}

	protected function getMonologHandler()
	{
		return (new StreamHandler(env('LOG_PATH').'/'.date('YmdH').'.log', Logger::DEBUG))
			->setFormatter(new LineFormatter(null, null, true, true));
	}

	public function basePath($path = null)
	{
		if (isset($this->basePath)) {
			return $this->basePath.($path ? '/'.$path : $path);
		}

		$this->basePath = realpath(getcwd().'/../');
		return $this->basePath($path);
	}

	public function run($request = null)
	{
		$response = $this->dispatch($request);

		if ($response instanceof symfonyResponse) {
			$response->send();
		} else {
			echo $response;
		}
	}

	public function make($abstract, array $parameters = [])
	{
		$abstract = $this->getAlias($this->normalize($abstract));

		if (array_key_exists($abstract, $this->availableBindings) &&
			! array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
			$this->{$method = $this->availableBindings[$abstract]}();

			$this->ranServiceBinders[$method] = true;
		}

		return parent::make($abstract, $parameters);
	}

	public function group(array $attributes, Closure $callback)
	{
		$parentGroupAttributes = $this->groupAttributes;

//		if (isset($attributes['middleware']) && is_string($attributes['middleware'])) {
//			$attributes['middleware'] = explode('|', $attributes['middleware']);
//		}

		$this->groupAttributes = $attributes;

		call_user_func($callback, $this);

		$this->groupAttributes = $parentGroupAttributes;
	}

	public function get($uri, $action)
	{
		$this->addRoute('GET', $uri, $action);

		return $this;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return $this
	 */
	public function post($uri, $action)
	{
		$this->addRoute('POST', $uri, $action);

		return $this;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return $this
	 */
	public function put($uri, $action)
	{
		$this->addRoute('PUT', $uri, $action);

		return $this;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return $this
	 */
	public function patch($uri, $action)
	{
		$this->addRoute('PATCH', $uri, $action);

		return $this;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return $this
	 */
	public function delete($uri, $action)
	{
		$this->addRoute('DELETE', $uri, $action);

		return $this;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return $this
	 */
	public function options($uri, $action)
	{
		$this->addRoute('OPTIONS', $uri, $action);

		return $this;
	}

	public function addRoute($method, $uri, $action)
	{
		$action = $this->parseAction($action);

		if (isset($this->groupAttributes)) {
			if (isset($this->groupAttributes['prefix'])) {
				$uri = trim($this->groupAttributes['prefix'], '/').'/'.trim($uri, '/');
			}

			if (isset($this->groupAttributes['suffix'])) {
				$uri = trim($uri, '/').rtrim($this->groupAttributes['suffix'], '/');
			}

			$action = $this->mergeGroupAttributes($action);
		}

		$uri = '/'.trim($uri, '/');

		if (isset($action['as'])) {
			$this->namedRoutes[$action['as']] = $uri;
		}

		if (is_array($method)) {
			foreach ($method as $verb) {
				$this->routes[$verb.$uri] = ['method' => $verb, 'uri' => $uri, 'action' => $action];
			}
		} else {
			$this->routes[$method.$uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];
		}
	}

	protected function mergeGroupAttributes(array $action)
	{
		return $this->mergeNamespaceGroup(
			$this->mergeMiddlewareGroup($action)
		);
	}

	protected function mergeNamespaceGroup(array $action)
	{
		if (isset($this->groupAttributes['namespace']) && isset($action['uses'])) {
			$action['uses'] = $this->groupAttributes['namespace'].'\\'.$action['uses'];
		}

		return $action;
	}

	protected function mergeMiddlewareGroup($action)
	{
		if (isset($this->groupAttributes['middleware'])) {
			if (isset($action['middleware'])) {
				$action['middleware'] = array_merge($this->groupAttributes['middleware'], $action['middleware']);
			} else {
				$action['middleware'] = $this->groupAttributes['middleware'];
			}
		}

		return $action;
	}

	protected function parseAction($action)
	{
		if (is_string($action)) {
			return ['uses' => $action];
		} elseif (! is_array($action)) {
			return [$action];
		}

//		if (isset($action['middleware']) && is_string($action['middleware'])) {
//			$action['middleware'] = explode('|', $action['middleware']);
//		}

		return $action;
	}

	protected function dispatch($request = null)
	{
		list ($method, $pathInfo) = $this->parseRequest($request);
		try {
			if (isset($this->routes[$method . $pathInfo])) {
				$this->currRoute = $this->routes[$method . $pathInfo];
				return $this->handleFoundRoute();
			} else {
				return $this->handleDispatcherResponse(
					$this->createDispatcher()->dispatch($method, $pathInfo)
				);
			}
		} catch (Exception $e) {
			//var_dump($e);
			return $this->sendExceptionToHandler($e);
		}
	}

	protected function handleFoundRoute()
	{
//		return $this->prepareResponse(
//			$this->callActionOnArrayBasedRoute()
//		);

		$action = $this->currRoute['action'];

		if (isset($action['uses'])) {
			return $this->prepareResponse($this->callControllerAction($action['uses']));
		}

		foreach ($action as $value) {
			if ($value instanceof Closure) {
				//$closure = $value->bindTo(new RoutingClosure);
				break;
			}
		}

		try {
			return $this->prepareResponse($this->call($value, []));
		} catch (HttpResponseException $e) {
			return $e->getResponse();
		}

	}

	public function path()
	{
		return $this->basePath.DIRECTORY_SEPARATOR.'app';
	}

	protected function callControllerAction($uses)
	{
		if (is_string($uses) && ! strpos($uses, '@')) {
			$uses .= '@';
		}

		list($controller, $method) = explode('@', $uses);

		if (empty($method)) {
			$method = 'run';
		}

		if (! method_exists($instance = $this->make($controller), $method)) {
			throw new NotFoundHttpException;
		}

		return $this->callControllerCallable(
			[$instance, $method],[]
		);
	}

	protected function callControllerCallable(callable $callable, array $parameters = [])
	{
		try {
			return $this->prepareResponse(
				$this->call($callable, $parameters)
			);
		} catch (HttpResponseException $e) {
			return $e->getResponse();
		}
	}

	protected function handleDispatcherResponse($routeInfo)
	{
		switch ($routeInfo[0]) {
			case Dispatcher::NOT_FOUND:
				throw new NotFoundHttpException;

//			case Dispatcher::METHOD_NOT_ALLOWED:
//				throw new MethodNotAllowedHttpException($routeInfo[1]);

			case Dispatcher::FOUND:
				return $this->handleFoundRoute();
		}
	}

	protected function createDispatcher()
	{
		return $this->dispatcher ?: \FastRoute\simpleDispatcher(function ($r) {
			foreach ($this->routes as $route) {
				$r->addRoute($route['method'], $route['uri'], $route['action']);
			}
		});
	}

	protected function prepareResponse($response)
	{
		if ($response instanceof PsrResponseInterface) {
			$response = (new HttpFoundationFactory)->createResponse($response);
		} elseif (! $response instanceof SymfonyResponse) {
			$response = new symfonyResponse($response);
		} elseif ($response instanceof BinaryFileResponse) {
			$response = $response->prepare(Request::capture());
		}

		return $response;
	}

	/*
	 * 解析request
	 * @return array
	 */
	protected function parseRequest($request)
	{
		if ($request instanceof symfonyRequest) {
			return array (
				$request->getMethod(),
				$request->getPathInfo()
			);
		}

		return array(
			$this->getMethod(),
			$this->getPathInfo()
		);
	}

	protected function getMethod()
	{
		if (isset($_POST['_method'])) {
			return strtoupper($_POST['_method']);
		} else {
			return $_SERVER['REQUEST_METHOD'];
		}
	}

	protected function getPathInfo()
	{
		$query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
		return '/'.trim(str_replace('?'.$query, '', $_SERVER['REQUEST_URI']), '/');
	}

	public function withFacades($aliases = true, $userAliases = [])
	{
		Facade::setFacadeApplication($this);

		if ($aliases) {
			$this->withAliases($userAliases);
		}
	}

	public function withAliases($userAliases = [])
	{
		$defaults = [
//			'Illuminate\Support\Facades\Auth' => 'Auth',
//			'Illuminate\Support\Facades\Cache' => 'Cache',
			'Libs\Support\Facades\DB' => 'DB',
//			'Illuminate\Support\Facades\Event' => 'Event',
//			'Illuminate\Support\Facibsades\Gate' => 'Gate',
//			'Illuminate\Support\Facades\Log' => 'Log',
//			'Illuminate\Support\Facades\Queue' => 'Queue',
//			'Illuminate\Support\Facades\Schema' => 'Schema',
//			'Illuminate\Support\Facades\URL' => 'URL',
//			'Illuminate\Support\Facades\Validator' => 'Validator',
		];

		if (! static::$aliasesRegistered) {
			static::$aliasesRegistered = true;

			$merged = array_merge($defaults, $userAliases);

			foreach ($merged as $original => $alias) {
				class_alias($original, $alias);
			}
		}
	}

	public function runningInConsole()
	{
		return php_sapi_name() == 'cli';
	}
}
