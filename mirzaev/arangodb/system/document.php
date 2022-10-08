<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

use mirzaev\arangodb\terminal;

use ArangoDBClient\Connection as _connection;
use ArangoDBClient\Document as _document;
use ArangoDBClient\Edge as _edge;
use ArangoDBClient\DocumentHandler as _document_handler;
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
     * @param _connection $session Сессия соединения с базой данных
     * @param string $collection Коллекция
     * @param ?array $data Данные
     * @param ?array $metadata Метаданные
     * @param bool $check Проверка на запись в базу данных
     *
     * @return string|null Идентификатор
     */
    public static function write(_connection $session, string $collection, ?array $data = null, ?array $metadata = null, bool $check = true): ?string
    {
        // Инициализация коллекции
        collection::init($session, $collection, isset($data['_from'], $data['_to']));

        if (isset($metadata['_from'], $metadata['_to'])) {
            // Ребро

            // Инициализация обработчика рёбер
            $documents = new _edge_handler($session);

            // Инициализация ребра
            $document = new _edge();

            // Инициализация вершин
            $_from = $metadata['_from'];
            $_to = $metadata['_to'];

            // Деинициализация из входных данных
            unset($metadata['_from'], $metadata['_to']);
        } else {
            // Вершина

            // Инициализация обработчика вершин
            $documents = new _document_handler($session);

            // Инициализация вершины
            $document = new _document();
        }

        foreach (['data' => $data, 'metadata' => ($metadata ?? []) + ['created' => time()]] as $key => $value) {
            // Перебор параметров

            // Запись в инстанцию документа
            $document->set($key, $value);
        }

        // Запись на сервер и его ответ в буфер возврата
        $id = isset($_from, $_to) ? $documents->saveEdge($collection, $_from, $_to, $document) : $documents->insert($collection, $document);

        if ($check && $documents->has($collection, $id)) {
            // Документ записан

            // Запись в вывод
            terminal::write("В коллекции \"$collection\" создан документ \"$id\"");
        }

        // Возврат идентификатора коллекции
        return $id;
    }

    /**
     * Обновить
     *
     * @param _connection $session Сессия соединения с базой данных
     * @param _document $document Инстанция документа вершины
     *
     * @return bool Статус обработки
     */
    public static function update(_connection $session, _document $document): bool {
        // Инициализация обработчика вершин
        $documents = new _document_handler($session);

        // Запись в базу данных
        return $documents->update($document);
    }
}
