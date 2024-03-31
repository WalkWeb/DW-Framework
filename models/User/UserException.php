<?php

declare(strict_types=1);

namespace Models\User;

class UserException
{
    public const LOGIN_ALREADY_EXIST          = 'User with this login already exists';
    public const EMAIL_ALREADY_EXIST          = 'User with this email already exists';
    public const INVALID_LOGIN_OR_PASSWORD    = 'Invalid login or password';

    // id
    public const INVALID_ID                    = 'Incorrect parameter "id", it required and type string';
    public const INVALID_ID_VALUE              = 'Incorrect parameter "id", excepted uuid';
    // login
    public const INVALID_LOGIN                 = 'Incorrect parameter "login", it required and type string';
    public const INVALID_LOGIN_LENGTH          = 'Incorrect parameter "login", should be min-max length: ';
    public const INVALID_LOGIN_SYMBOL          = 'Incorrect "login", symbol, excepted letters, numbers, hyphen or underscore';
    // password
    public const INVALID_PASSWORD              = 'Incorrect parameter "password", it required and type string';
    public const INVALID_PASSWORD_LENGTH       = 'Incorrect parameter "password", should be min-max length: ';
    // email
    public const INVALID_EMAIL                 = 'Incorrect parameter "email", it required and type string';
    public const INVALID_EMAIL_LENGTH          = 'Incorrect parameter "email", should be min-max length: ';
    public const INVALID_EMAIL_SYMBOL          = 'Incorrect email';
    // reg_complete
    public const INVALID_REG_COMPLETE          = 'Incorrect parameter "reg_complete", excepted int (0 or 1)';
    // email_verified
    public const INVALID_EMAIL_VERIFIED        = 'Incorrect parameter "email_verified", excepted int (0 or 1)';
    // auth_token
    public const INVALID_AUTH_TOKEN            = 'Incorrect parameter "auth_token", it required and type string';
    public const INVALID_AUTH_TOKEN_LENGTH     = 'Incorrect parameter "auth_token", should be min-max length: ';
    // verified_token
    public const INVALID_VERIFIED_TOKEN        = 'Incorrect parameter "verified_token", it required and type string';
    public const INVALID_VERIFIED_TOKEN_LENGTH = 'Incorrect parameter "verified_token", should be min-max length: ';
    // created_at
    public const INVALID_CREATED_AT            = 'Incorrect parameter "created_at", it required and type string';
    public const INVALID_CREATED_AT_VALUE      = 'Incorrect parameter "created_at", expected date';
}
