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
        echo self::generate_prefix() . ' ' . $text . PHP_EOL;
    }

    /**
     * Генерация префикса
     */
    public static function generate_prefix(): string
    {
        return '[' . self::PREFIX . ']';
    }
}
