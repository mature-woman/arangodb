<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

use mirzaev\arangodb\terminal;

use ArangoDBClient\Connection as _connection;
use ArangoDBClient\Document as _document;
use ArangoDBClient\Edge as _edge;
use ArangoDBClient\CollectionHandler as _collection_handler;
use ArangoDBClient\EdgeHandler as _edge_handler;

/**
 * Документ
 *
 * @package mirzaev\arangodb
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
class document
{
    /**
     * Записать
     *
     * @param _connection $connection Инстанция соединения
     * @param string $collection Коллекция
     * @param array $data Данные
     *
     * @return string|null Идентификатор
     */
    public static function write(_connection $connection, string $collection, array $data): ?string
    {
        // Инициализация коллекции
        collection::init($connection, $collection, isset($data['_from'], $data['_to']));

        if (isset($data['_from'], $data['_to'])) {
            // Ребро

            // Инициализация обработчика рёбер
            $documents = new _edge_handler($connection);

            // Инициализация ребра
            $document = new _edge();

            // Инициализация вершин
            $_from = $data['_from'];
            $_to = $data['_to'];

            // Деинициализация из входных данных
            unset($data['_from'], $data['_to']);
        } else {
            // Вершина

            // Инициализация обработчика вершин
            $documents = new _collection_handler($connection);

            // Инициализация вершины
            $document = new _document();
        }

        // Настройка
        foreach ($data as $key => $value) {
            // Перебор параметров

            $document->set($key, $value);
        }

        // Запись на сервер и его ответ в буфер возврата
        $id = isset($_from, $_to) ? $documents->saveEdge($collection, $_from, $_to, $document) : $documents->save($collection, $document);

        if ($documents->has($collection, $id)) {
            // Документ записан

            // Запись в вывод
            terminal::write("В коллекции \"$collection\" создан документ \"$id\"");

            // Возврат идентификатора коллекции
            return $id;
        }
    }
}
