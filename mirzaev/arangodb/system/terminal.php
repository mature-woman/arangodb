<?php

declare(strict_types=1);

namespace mirzaev\arangodb;

class terminal
{
    /**
     * Префикс
     */
    protected const PREFIX = 'arangodb';

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
