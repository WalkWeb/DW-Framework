<?php

declare(strict_types=1);

namespace WalkWeb\NW;

class Translation
{
    private const DEFAULT_LANGUAGE = 'en';

    /**
     * Массив переводов в формате:
     *
     * 'оригинал' => 'перевод'
     *
     * @var array
     */
    private array $messages;

    /**
     * @var string
     */
    private string $language;

    private Container $container;

    /**
     * @param Container $container
     * @param string|null $language
     * @param array|null $messages
     * @param string|null $defaultLanguage
     * @throws AppException
     */
    public function __construct(
        Container $container,
        ?string $language = null,
        ?array $messages = null,
        ?string $defaultLanguage = null
    )
    {
        $this->container = $container;

        if ($defaultLanguage === null) {
            $defaultLanguage = self::DEFAULT_LANGUAGE;
        }

        $this->language = $language ?? $this->defineLanguage($defaultLanguage);
        $this->messages = $messages ?? $this->getMessages($this->language, $defaultLanguage);
    }

    /**
     * @param string $message
     * @return string
     */
    public function trans(string $message): string
    {
        if (!array_key_exists($message, $this->messages)) {
            return $message;
        }

        // Если перевод отсутствует - просто возвращаем это же сообщение
        if (!is_string($this->messages[$message])) {
            return $message;
        }

        return $this->messages[$message];
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $defaultLanguage
     * @return string
     */
    private function defineLanguage(string $defaultLanguage): string
    {
        if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) || !is_string($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->language = $defaultLanguage;
            return $this->language;
        }

        $this->language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) ?: $defaultLanguage;
        return $this->language;
    }

    /**
     * @param string $language
     * @param string $defaultLanguage
     * @return array
     * @throws AppException
     */
    private function getMessages(string $language, string $defaultLanguage): array
    {
        $path = $this->container->getTranslateDir() . $language . '/messages.php';

        if (!file_exists($path)) {

            $path = $this->container->getTranslateDir() . $defaultLanguage . '/messages.php';

            if (!file_exists($path)) {
                throw new AppException('Default language messages file not found');
            }
        }

        return require $path;
    }
}
