<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

// Built-in libraries
use exception;

/**
 * Terminal
 *
 * lol sorry 
 * i did not want to do this
 *
 * @package mirzaev\arangodb
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
class terminal
{
    /**
     * Префикс
     */
    protected const string PREFIX = 'arangodb';

    /**
     * Запись в вывод
     *
     * @param string $text Текст сообщения
     */
    public static function write(string $text): void
    {
        echo sprintf("[%s] $text\n", self::PREFIX);
    }
}
