<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

// Files of the project
use mirzaev\arangodb\terminal,
	mirzaev\arangodb\connection,
	mirzaev\arangodb\enumerations\collection\type;

// Librsry for ArangoDB
use ArangoDBClient\Connection as _connection,
	ArangoDBClient\Document as _document,
	ArangoDBClient\Edge as _edge,
	ArangoDBClient\DocumentHandler as _document_handler,
	ArangoDBClient\EdgeHandler as _edge_handler;

// Built-in libraries
use exception;

/**
 * Document
 *
 * Handlers of document in ArangoDB
 *
 * @package mirzaev\arangodb
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
class document
{
	/**
	 * Write
	 *
	 * @param string $collection Коллекция
	 * @param ?array $data Данные
	 * @param bool $check Проверка на запись в базу данных
	 * @param ?terminal $terminal Instance of the terminal
	 * @param array &$errors Registry of errors
	 *
	 * @return string|null Идентификатор
   *
   * @todo
   * 1. Bind parameters
	 */
	public static function write(
		string $collection,
		?array $data = [],
		bool $check = false,
		?terminal $terminal = null,
		array &$errors = []
	): string|null|false {
		try {
			// Инициализация коллекции
			collection::initialize($collection, isset($data['_from'], $data['_to']) ? type::edge : type::document);

			if (isset($data['_from'], $data['_to'])) {
				// Ребро

				// Инициализация обработчика рёбер
				$documents = new _edge_handler(connection::$session);

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
				$documents = new _document_handler(connection::$session);

				// Инициализация вершины
				$document = new _document();
			}

			// Инициализация даты создания
			$created = time();

			foreach (['created' => $created, 'updated' => $created] + $data as $key => $value) {
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
		} catch (exception $e) {
			// Writing to registry of errors
			$errors[] = [
				'text' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'stack' => $e->getTrace()
			];
		}

		// Exit (fail)
		return false;
	}

	/**
	 * Update
	 *
	 * @param array &$errors Registry of errors
	 * @param _document $document Instance of the document
	 *
	 * @return bool Has the document been updated?
	 */
	public static function update(_document $document, array &$errors = []): bool
	{
		try {
			// Initialize the handler of documents
			$documents = new _document_handler(connection::$session);

			// Writing date of the updating to the updating document
			$document->set('updated', time());

			// Writing and exit (success)
			return $documents->update($document);
		} catch (exception $e) {
			// Writing to registry of errors
			$errors[] = [
				'text' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'stack' => $e->getTrace()
			];
		}

		// Exit (fail)
		return false;
	}

	/**
	 * Delete
	 *
	 * @param _document $document Instance of the document
	 * @param array &$errors Registry of errors
	 *
	 * @return bool Has the document been deleted?
	 */
	public static function delete(_document $document, array &$errors = []): bool
	{
		try {
			// Initialize the handler of documents
			$documents = new _document_handler(connection::$session);

			// Delete abd exit (success)
			return $documents->remove($document);
		} catch (exception $e) {
			// Writing to registry of errors
			$errors[] = [
				'text' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'stack' => $e->getTrace()
			];
		}

		// Exit (fail)
		return false;
	}
}
