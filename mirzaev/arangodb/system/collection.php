<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

// Files of the project
use mirzaev\arangodb\connection,
	mirzaev\arangodb\terminal,
	mirzaev\arangodb\enumerations\collection\type;

// Library for ArangoDB
use ArangoDBClient\Statement as _statement,
	ArangoDBClient\Document as _document,
	ArangoDBClient\CollectionHandler as _collection_handler;

// Built-in libraries
use exception;

/**
 * Collection
 *
 * @package mirzaev\arangodb
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
class collection
{
	/**
	 * Initialize a collection
	 *
	 * @param string $collection Name of the collection
	 * @param type $type Type of the collection
	 * @param ?terminal $terminal Инстанция терминала
	 * @param array &$errors Registry of errors
	 *
	 * @return string|null Identifier of the collection
	 */
	public static function initialize(string $name, type $type = type::document, ?terminal $terminal = null, array &$errors = []): ?string
	{
		try {
			// Инициализация
			$collections = new _collection_handler(connection::$session);

			if (!$collections->has($name)) {
				// Не найдана коллекция

				// Запись в вывод
				if ($terminal instanceof terminal) $terminal::write("Not found $type collection: $name");

				// Запись коллекции на сервер и его ответ в буфер возврата
				$id = $collections->create($name, ['type' => $type->code()]);

				if ($collections->has($name)) {
					// Коллекция найдена (записана)

					// Запись в вывод
					if ($terminal instanceof terminal) $terminal::write("Created $type collection: $name");

					// Возврат идентификатора коллекции
					return $id;
				}
			} else {
				// Найдена коллекция

				// Возврат идентификатора коллекции
				return $name;
			}
		} catch (exception $e) {
			// Writing to registry of errors
			$errors[] = [
				'text' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'stack' => $e->getTrace()
			];
		}

		return null;
	}

	/**
	 * Execute
	 *
	 * @param string $query Query (AQL)
	 * @param array $parameters Binded parameters for placeholders [placholder => parameter]
	 * @param array &$errors Registry of errors
	 *
	 * @return _document|array|string|int|null Instance of the document
	 */
	public static function execute(string $query, array $parameters = [], array &$errors = []): _document|string|array|int|null
	{
		try {
			// Statement instance initialization
			$instance = new _statement(
				connection::$session,
				[
					'query' => $query,
					"batchSize" => 1000,
					"sanitize"  => true
				]
			);

			// Binds application
			$instance->bind($parameters);

			// Sending the request
			$response = $instance->execute();

			// Инициализация буфера вывода
			$buffer = [];

			foreach ($response->getAll() as $key => $value) {
				$buffer[$key] = $value;
			}

			// Exit (success)
			return is_array($buffer) && count($buffer) > 1 ? $buffer : $buffer[0] ?? null;
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
		return null;
	}

	/**
	 * Truncate
	 *
	 * @param string $name Name of the collection
	 * @param array &$errors Registry of errors
	 *
	 * @return bool Статус выполнения
	 */
	public static function truncate(string $collection, array &$errors = []): bool
	{
		try {
			// Initizlizing of the collection handler
			$collections = new _collection_handler(connection::$session);

			if ($collections->has($collection)) {
				// Fount the collection

				// Truncate and exit (success)
				return $collections->truncate($collection);
			}
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
	 * Count documents in ArangoDB
	 *
	 * @param string $collection Name of the collection 
	 * @param array &$errors Registry of errors
	 *
	 * @return int|null Amount of documents in ArangoDB
	 */
	public static function count(string $collection, array &$errors = []): ?int
	{
		try {
			// Count and exit (success)
			return static::execute(
				<<<'AQL'
					RETURN LENGTH(@collection)
				AQL,
				[
					'collection' => $collection
				]
			);
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
		return null;
	}
}
