<?php

declare(strict_types=1);

namespace Domain\User;

use DateTimeInterface;

interface UserInterface
{
    public const AUTH_TOKEN                = 'auth';

    public const LOGIN_MIN_LENGTH          = 4;
    public const LOGIN_MAX_LENGTH          = 14;
    public const PASSWORD_MIN_LENGTH       = 5;
    public const PASSWORD_MAX_LENGTH       = 60;
    public const EMAIL_MIN_LENGTH          = 6;
    public const EMAIL_MAX_LENGTH          = 30;
    public const AUTH_TOKEN_MIN_LENGTH     = 30;
    public const AUTH_TOKEN_MAX_LENGTH     = 30;
    public const VERIFIED_TOKEN_MIN_LENGTH = 30;
    public const VERIFIED_TOKEN_MAX_LENGTH = 30;
    public const TEMPLATE_MIN_LENGTH       = 2;
    public const TEMPLATE_MAX_LENGTH       = 10;

    /**
     * Возвращает uuid пользователя
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Возвращает логин пользователя
     *
     * @return string
     */
    public function getLogin(): string;

    /**
     * Возвращает hash пароля пользователя
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Возвращает email пользователя
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Завершена ли регистрация
     *
     * @return bool
     */
    public function isRegComplete(): bool;

    /**
     * Подтвержден ли email
     *
     * @return bool
     */
    public function isEmailVerified(): bool;

    /**
     * Указывает, что email подтвержден
     */
    public function emailVerified(): void;

    /**
     * Возвращает авторизационный токен
     *
     * @return string
     */
    public function getAuthToken(): string;

    /**
     * Возвращает токен верификации
     *
     * @return string
     */
    public function getVerifiedToken(): string;

    /**
     * Возвращает шаблон дизайна сайта который будет использоваться для данного пользователя
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Возвращает дату регистрации пользователя
     *
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface;
}
