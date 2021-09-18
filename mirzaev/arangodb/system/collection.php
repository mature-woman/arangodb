<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

use mirzaev\arangodb\terminal;

use ArangoDBClient\Connection as _connection;
use ArangoDBClient\Collection as _collection;
use ArangoDBClient\CollectionHandler as _collection_handler;

/**
 * Коллекция
 *
 * @package mirzaev\arangodb
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
class collection
{
    /**
     * Инициализация
     *
     * @param _connection $connection Инстанция соединения
     * @param string $name Название
     * @param bool $edge Это ребро? (иначе: вершина)
     *
     * @return string|null Идентификатор
     */
    public static function init(_connection $connection, string $name, bool $edge = false): ?string
    {
        // Инициализация
        $collections = new _collection_handler($connection);

        if (!$collections->has($name)) {
            // Коллекция не найдена

            // Запись в вывод
            terminal::write("Коллекция \"$name\" не найдена");

            // Инициализация
            $collection = new _collection();

            // Настройка
            $collection->setName($name);
            $collection->setType($edge ? 'edge' : 'document');

            // Запись коллекции на сервер и его ответ в буфер возврата
            $id = $collections->create($name);

            if ($collections->has($name)) {
                // Коллекция найдена (записана)

                // Запись в вывод
                terminal::write("Создана коллекция \"$name\"");

                // Возврат идентификатора коллекции
                return $id;
            }
        }
    }

    /**
     * Поиск
     *
     * @param _connection $connection Соединение
     * @param string $collection Название
     * @param array $condition Условия
     *
     * @return array|null Коллекция, если найдена
     */
    public static function search(_connection $connection, string $name, array $condition): ?array
    {
        // Инициализация
        $collections = new _collection_handler($connection);

        if ($collections->has($name)) {
            // Коллекция найдена

            return $collections->byExample($name, $condition)->getAll();
        }
    }
}
