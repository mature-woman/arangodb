<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

// Library for ArangoDB
use ArangoDBClient\Connection as _connection,
	ArangoDBClient\Exception as _exception,
	ArangoDBClient\UpdatePolicy as _update,
	ArangoDBClient\ConnectionOptions as _options;

// Built-in libraries
use exception;

/**
 * Connection
 *
 * @package mirzaev\arangodb
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
final class connection
{
	/**
	 * Адрес
	 */
	protected string $adress = 'tcp://127.0.0.1:8529';

	/**
	 * Хранилище (название)
	 */
	protected string $storage = 'root';

	/**
	 * Тип аутентификации
	 */
	protected string $auth = 'Basic';

	/**
	 * Псевдоним пользователя
	 */
	protected string $name = 'root';

	/**
	 * Пароль пользователя
	 */
	protected string $password = '';

	/**
	 * Тип соединения
	 */
	protected string $connection = 'Keep-Alive';

	/**
	 * Время ожидания соединения
	 */
	protected int $timeout_connect = 3;

	/**
	 * Время ожидания запроса
	 */
	protected int $timeout_request = 3;

	/**
	 * Переподключиться ли при разрыве соединения
	 */
	protected bool $reconnect = true;

	/**
	 * Создавать ли коллекцию, если она не существует
	 */
	protected bool $create = true;

	/**
	 * Действия при обновлении коллекции
	 */
	protected string $update = _update::LAST;

	/**
	 * Активация журналирования
	 */
	protected bool $journal = false;

	/**
	 * Сессия соединения
	 */
	public static _connection $session;

	/**
	 * Конструктор
	 *
	 * @param array $settings Настройки
   *
   * @todo ПЕРЕДЕЛАТЬ ПОЛНОСТЬЮ
	 */
	public function __construct(array $settings = null)
	{
		// Запись
		@$this->__set('adress', $settings['adress'] ?? $settings['endpoint']);
		@$this->__set('storage', $settings['storage'] ?? $settings['database']);
		@$this->__set('auth', $settings['auth']);
		@$this->__set('name', $settings['name']);
		@$this->__set('password', $settings['password']);
		@$this->__set('connection', $settings['connection']);
		@$this->__set('timeout_connect', $settings['timeout_connect']);
		@$this->__set('timeout_request', $settings['timeout_request']);
		@$this->__set('reconnect', $settings['reconnect']);
		@$this->__set('create', $settings['create']);
		@$this->__set('update', $settings['update'] ?? $settings['policy']);
		@$this->__set('journal', $settings['journal']);

		if ($this->journal) {
			// Запрос на активацию журналирования

			_exception::enableLogging();
		}

		// Подключение
		static::$session = new _connection([
			_options::OPTION_ENDPOINT => $this->adress,
			_options::OPTION_DATABASE => $this->storage,
			_options::OPTION_AUTH_TYPE => $this->auth,
			_options::OPTION_AUTH_USER => $this->name,
			_options::OPTION_AUTH_PASSWD => $this->password,
			_options::OPTION_CONNECTION => $this->connection,
			_options::OPTION_CONNECT_TIMEOUT => $this->timeout_connect,
			_options::OPTION_REQUEST_TIMEOUT => $this->timeout_request,
			_options::OPTION_RECONNECT => $this->reconnect,
			_options::OPTION_CREATE => $this->create,
			_options::OPTION_UPDATE_POLICY => $this->update,
		]);
	}

	/**
	 * Записать свойство
	 *
	 * @param mixed $name Название
	 * @param mixed $value Значение
	 */
	public function __set(string $name, mixed $value): void
	{
		match ($name) {
			'adress', 'endpoint' => $this->adress = $value ?? throw new exception("Свойство \"$name\" не может быть пустым", 500),
			'storage', 'database', 'db' => $this->storage = $value ?? throw new exception("Свойство \"$name\" не может быть пустым", 500),
			'auth' => !$value ?: $this->auth = $value,
			'name' => !$value ?: $this->name = $value,
			'password' => !$value ?: $this->password = $value,
			'connection' => !$value ?: $this->connection = $value,
			'timeout_connect' => !$value ?: $this->timeout_connect = $value,
			'timeout_request' => !$value ?: $this->timeout_request = $value,
			'reconnect' => !$value ?: $this->reconnect = $value,
			'create' => !$value ?: $this->create = $value,
			'update', 'policy' => !$value ?: $this->update = $value,
			'journal' => !$value ?: $this->journal = $value,
			default => throw new exception("Свойство \"$name\" не найдено", 404)
		};
	}

	/**
	 * Прочитать свойство
	 *
	 * @param mixed $name Название
	 */
	public function __get(string $name): mixed
	{
		return match ($name) {
			'adress', 'endpoint' => $this->adress,
			'storage', 'database', 'db' => $this->storage,
			'auth' => $this->auth,
			'name' => $this->name,
			'password' => $this->password,
			'connection' => $this->connection,
			'timeout_connect' => $this->timeout_connect,
			'timeout_request' => $this->timeout_request,
			'reconnect' => $this->reconnect,
			'create' => $this->create,
			'update', 'policy' => $this->update,
			'journal' => $this->journal,
			default => throw new exception("Свойство \"$name\" не найдено", 404)
		};
	}

	/**
	 * Проверить свойство на инициализированность
	 *
	 * @param mixed $name Название
	 */
	public function __isset(string $name): bool
	{
		return match ($name) {
			'adress', 'endpoint' => isset($this->adress),
			'storage', 'database', 'db' => isset($this->storage),
			'auth' => isset($this->auth),
			'name' => isset($this->name),
			'password' => isset($this->password),
			'connection' => isset($this->connection),
			'timeout_connect' => isset($this->timeout_connect),
			'timeout_request' => isset($this->timeout_request),
			'reconnect' => isset($this->reconnect),
			'create' => isset($this->create),
			'update', 'policy' => isset($this->update),
			'journal' => isset($this->journal),
			default => throw new exception("Свойство \"$name\" не найдено", 404)
		};
	}
}
