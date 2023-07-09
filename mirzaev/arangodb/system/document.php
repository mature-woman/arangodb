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
     * @param bool $check Проверка на запись в базу данных
     * @param ?terminal $terminal Инстанция терминала
     *
     * @return string|null Идентификатор
     */
    public static function write(_connection $session, string $collection, ?array $data = [], bool $check = false, ?terminal $terminal = null): ?string
    {
        // Инициализация коллекции
        collection::init($session, $collection, isset($data['_from'], $data['_to']));

        if (isset($data['_from'], $data['_to'])) {
            // Ребро

            // Инициализация обработчика рёбер
            $documents = new _edge_handler($session);

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
            $documents = new _document_handler($session);

            // Инициализация вершины
            $document = new _document();
        }

        foreach (['created' => time()] + $data as $key => $value) {
            // Перебор параметров

            // Запись в инстанцию документа
            $document->set($key, $value);
        }

        // Запись на сервер и его ответ в буфер возврата
        $id = isset($_from, $_to) ? $documents->saveEdge($collection, $_from, $_to, $document) : $documents->insert($collection, $document);

        if ($check && $documents->has($collection, $id)) {
            // Найден записанный документ

            // Запись в вывод
            if ($terminal instanceof terminal) $terminal::write("В коллекции \"$collection\" создан документ \"$id\"");
        } else if ($check) {
            // Не найден записанный документ

            // Запись в вывод
            if ($terminal instanceof terminal) $terminal::write("В коллекции \"$collection\" не удалось найти созданный или создать документ");

            return null;
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
    public static function update(_connection $session, _document $document): bool
    {
        // Инициализация обработчика вершин
        $documents = new _document_handler($session);

        // Запись в базу данных
        return $documents->update($document);
    }
}
