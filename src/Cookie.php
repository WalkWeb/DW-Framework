<?php

namespace WalkWeb\NW;

class Cookie
{
    private array $cookies;

    public function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    /**
     * Возвращает текущие куки
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->cookies;
    }

    /**
     * Устанавливает куки
     *
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        $this->cookies[$name] = $value;
    }

    /**
     * Возвращает куки по указанному имени, если они есть
     *
     * @param string $name
     * @return null|string
     */
    public function get(string $name): ?string
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Проверяет наличие куков по имени
     *
     * @param string $name
     * @return bool
     */
    public function check(string $name): bool
    {
        return !empty($this->cookies[$name]);
    }

    /**
     * Удаляет куки по имени
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        unset($this->cookies[$name]);
    }
}
