<?php

declare(strict_types=1);

namespace NW\Response;

final class Emitter
{
    /**
     * Создает ответ сервера на основе Response
     *
     * @param Response $response
     */
    public static function emit(Response $response): void
    {
        header(sprintf(
            'HTTP/%s %d',
            $response->getProtocolVersion(),
            $response->getStatusCode()
        ));

        foreach ($response->getHeaders() as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $response->getBody();
    }
}
