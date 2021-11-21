<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Request;

use NW\Request\ServerRequestFactory;
use Tests\AbstractTestCase;

class ServerRequestFactoryTest extends AbstractTestCase
{
    public function testServerRequestFactoryDefault(): void
    {
        $protocol = 'HTTP/1.1';
        $method = 'GET';
        $uri = '/';

        $request = ServerRequestFactory::fromGlobals();

        self::assertEquals($_SERVER, $request->getServer());
        self::assertEquals($_POST, $request->getBody());
        self::assertEquals($_COOKIE, $request->getCookies());
        self::assertEquals($_GET, $request->getQuery());
        self::assertEquals($_FILES, $request->getFiles());

        self::assertEquals($uri, $request->getUri());
        self::assertEquals($protocol, $request->getProtocol());
        self::assertEquals($method, $request->getMethod());
    }

    public function testServerRequestFactoryCustom(): void
    {
        $server = $_SERVER;

        $server['REQUEST_URI'] = '/post/12';
        $server['SERVER_PROTOCOL'] = 'HTTP/2.0';
        $server['REQUEST_METHOD'] = 'DELETE';

        $body = [
            'title' => 'Post Title',
            'text' => 'Post text',
        ];

        $cookies = [
            'token' => '530bef96-38e0-4b93-9aa6-cfb6fd1b535b',
        ];

        $query = [
            'page' => 10,
        ];

        $files = [
            'example',
        ];

        $request = ServerRequestFactory::fromGlobals($server, $body, $cookies, $query, $files);

        self::assertEquals($server, $request->getServer());
        self::assertEquals($server['REQUEST_URI'], $request->getUri());
        self::assertEquals($server['SERVER_PROTOCOL'], $request->getProtocol());
        self::assertEquals($server['REQUEST_METHOD'], $request->getMethod());
        self::assertEquals($cookies, $request->getCookies());
        self::assertEquals($query, $request->getQuery());
        self::assertEquals($body, $request->getBody());
        self::assertEquals($files, $request->getFiles());
        self::assertEquals([], $request->getAttributes());
    }
}
