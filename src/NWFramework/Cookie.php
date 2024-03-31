<?php

namespace NW;

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
     * TODO Rename to getArray
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Устанавливает куки
     *
     * @param string $name
     * @param string $value
     */
    public function setCookie(string $name, string $value): void
    {
        $this->cookies[$name] = $value;
    }

    /**
     * Возвращает куки по указанному имени, если они есть
     *
     * @param string $name
     * @return null|string
     */
    public function getCookie(string $name): ?string
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * Проверяет наличие куков по имени
     *
     * @param string $name
     * @return bool
     */
    public function checkCookie(string $name): bool
    {
        return !empty($this->cookies[$name]);
    }

    /**
     * Удаляет куки по имени
     *
     * @param string $name
     */
    public function deleteCookie(string $name): void
    {
        unset($this->cookies[$name]);
    }
}
