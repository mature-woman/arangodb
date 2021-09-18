<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

use ArangoDBClient\Connection as _connection;
use ArangoDBClient\Exception as _exception;
use ArangoDBClient\UpdatePolicy as _update;
use ArangoDBClient\ConnectionOptions as _options;

use exception;

/**
 * Подключение
 *
 * @package mirzaev\arangodb
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
    protected _connection $session;

    /**
     * Конструктор
     *
     * @param array $settings Настройки
     */
    public function __construct(array $settings = null)
    {
        // Запись
        $this->__set('adress', $settings['adress'] ?? $settings['endpoint'] ?? null);
        $this->__set('storage', $settings['storage'] ?? $settings['database'] ?? null);
        $this->__set('auth', $settings['auth'] ?? null);
        $this->__set('name', $settings['name'] ?? null);
        $this->__set('password', $settings['password'] ?? null);
        $this->__set('connection', $settings['connection'] ?? null);
        $this->__set('timeout_connect', $settings['timeout_connect'] ?? null);
        $this->__set('timeout_request', $settings['timeout_request'] ?? null);
        $this->__set('reconnect', $settings['reconnect'] ?? null);
        $this->__set('create', $settings['create'] ?? null);
        $this->__set('update', $settings['update'] ?? $settings['policy'] ?? null);
        $this->__set('journal', $settings['journal'] ?? null);

        if ($this->journal) {
            // Запрос на активацию журналирования

            _exception::enableLogging();
        }

        // Подключение
        $this->session = new _connection([
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
            'auth' => $this->auth = $value,
            'name' => $this->name = $value,
            'password' => $this->password = $value,
            'connection' => $this->connection = $value,
            'timeout_connect' => $this->timeout_connect = $value,
            'timeout_request' => $this->timeout_request = $value,
            'reconnect' => $this->reconnect = $value,
            'create' => $this->create = $value,
            'update', 'policy' => $this->update = $value,
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
            'session' => $this->session,
            default => throw new exception("Свойство \"$name\" не найдено", 404)
        };
    }
}
