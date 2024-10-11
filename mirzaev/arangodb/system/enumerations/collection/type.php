<?php

declare(strict_types=1);

namespace mirzaev\arangodb\enumerations\collection;

// Library for ArangoDB
use ArangoDBClient\Collection as _collection;

/**
 * Types of collections
 *
 * @package mirzaev\arangodb\enumerations\collection
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
enum type
{
	case document;
	case edge;

	/**
	 * Read code of the type from library for ArangoDB
	 *
	 * @return int Code of the type
	 */
	public function code(): int
	{
		// Exit (success)
		return match ($this) {
			type::document => _collection::TYPE_DOCUMENT,
			type::edge => _collection::TYPE_EDGE
		};
	}
}
