<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

use mirzaev\arangodb\terminal;

use ArangoDBClient\Connection as _connection;
use ArangoDBClient\Collection as _collection;
use ArangoDBClient\Statement as _statement;
use ArangoDBClient\Document as _document;
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
     * @param _connection $session Сессия соединения с базой данных
     * @param string $name Название
     * @param bool $edge Обрабатывать как ребро? (иначе: вершина)
     * @param ?terminal $terminal Инстанция терминала
     *
     * @return string|null Идентификатор коллекции
     */
    public static function init(_connection $session, string $name, bool $edge = false, ?terminal $terminal = null): ?string
    {
        // Инициализация
        $collections = new _collection_handler($session);

        if (!$collections->has($name)) {
            // Не найдана коллекция

            // Запись в вывод
            if ($terminal instanceof terminal) $terminal::write("Коллекция \"$name\" не найдена");

            // Запись коллекции на сервер и его ответ в буфер возврата
            $id = $collections->create($name, ['type' => $edge ? _collection::TYPE_EDGE : _collection::TYPE_DOCUMENT]);

            if ($collections->has($name)) {
                // Коллекция найдена (записана)

                // Запись в вывод
                if ($terminal instanceof terminal) $terminal::write("Создана коллекция \"$name\" с типом " . ($edge ? 'ребро' : 'документ'));

                // Возврат идентификатора коллекции
                return $id;
            }
        } else {
            // Найдена коллекция

            // Возврат идентификатора коллекции
            return $name;
        }

        return null;
    }

    /**
     * Поиск
     *
     * @param _connection $session Сессия соединения с базой данных
     * @param string $query AQL-запрос
     *
     * @return _document|null Инстанция документа
     */
    public static function search(_connection $session, string $query): ?_document
    {
        // Поиск журнала
        $journal = (new _statement(
            $session,
            [
                'query' => $query,
                "batchSize" => 1000,
                "sanitize"  => true
            ]
        ))->execute();

        // Инициализация буфера вывода
        $buffer = [];

        foreach ($journal as $key => $value) {
            $buffer[$key] = $value;
        }

        return $buffer[0] ?? null;
    }

    /**
     * Очистка
     *
     * @param _connection $session Сессия соединения с базой данных
     * @param string $name Название
     *
     * @return bool Статус выполнения
     */
    public static function truncate(_connection $session, string $name): bool
    {
        // Инициализация
        $collections = new _collection_handler($session);

        if ($collections->has($name)) {
            // Найдена коллекция

            // Очистка
            return $collections->truncate($name);
        }

        return false;
    }
}
