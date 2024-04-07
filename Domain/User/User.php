<?php

declare(strict_types=1);

namespace Domain\User;

use DateTimeInterface;

class User implements UserInterface
{
    private string $id;
    private string $login;
    private string $password;
    private string $email;
    private bool $regComplete;
    private bool $emailVerified;
    private string $authToken;
    private string $verifiedToken;
    private string $template;
    private DateTimeInterface $createdAt;

    public function __construct(
        string $id,
        string $login,
        string $password,
        string $email,
        bool $regComplete,
        bool $emailVerified,
        string $authToken,
        string $verifiedToken,
        string $template,
        DateTimeInterface $createdAt
    )
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->regComplete = $regComplete;
        $this->emailVerified = $emailVerified;
        $this->authToken = $authToken;
        $this->verifiedToken = $verifiedToken;
        $this->template = $template;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isRegComplete(): bool
    {
        return $this->regComplete;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function emailVerified(): void
    {
        $this->emailVerified = true;
        // Помимо подтверждения email могут быть другие необходимые действия для завершения регистрации
        // Но в текущей простой версии подтверждении email автоматически завершает регистрацию
        $this->regComplete = true;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @return string
     */
    public function getVerifiedToken(): string
    {
        return $this->verifiedToken;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
