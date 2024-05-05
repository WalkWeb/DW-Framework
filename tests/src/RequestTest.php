<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\Cookie;
use WalkWeb\NW\Request;
use Tests\AbstractTest;

class RequestTest extends AbstractTest
{
    public function testCreateEmptyRequest(): void
    {
        $server = [];
        $protocol = 'HTTP/1.1';
        $method = 'GET';
        $uri = '/';

        $request = new Request($server);

        self::assertEquals($server, $request->getServer());
        self::assertEquals($uri, $request->getUri());
        self::assertEquals($protocol, $request->getProtocol());
        self::assertEquals($method, $request->getMethod());
        self::assertEquals(new Cookie(), $request->getCookies());
        self::assertEquals([], $request->getQuery());
        self::assertEquals([], $request->getBody());
        self::assertEquals([], $request->getFiles());
        self::assertEquals([], $request->getAttributes());
    }

    public function testCreateRequest(): void
    {
        $server = [
            'REQUEST_URI'     => '/about',
            'SERVER_PROTOCOL' => 'HTTP/2.0',
            'REQUEST_METHOD'  => 'POST',
        ];

        $body = [
            'title' => 'Post Title',
            'text'  => 'Post text',
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

        $request = new Request($server, $body, $cookies, $query, $files);

        self::assertEquals($server, $request->getServer());
        self::assertEquals($server['REQUEST_URI'], $request->getUri());
        self::assertEquals($server['SERVER_PROTOCOL'], $request->getProtocol());
        self::assertEquals($server['REQUEST_METHOD'], $request->getMethod());
        self::assertEquals(new Cookie($cookies), $request->getCookies());
        self::assertEquals($query, $request->getQuery());
        self::assertEquals($body, $request->getBody());
        self::assertEquals($files, $request->getFiles());
        self::assertEquals([], $request->getAttributes());
    }

    public function testRequestAttributes(): void
    {
        $request = new Request([]);

        $customAttributeName = 'custom_attribute';
        $customAttributeValue = 'custom_attribute_value';
        $customAttributeDefault = 'custom_attribute_default';

        self::assertNull($request->getAttribute($customAttributeName));
        self::assertEquals($customAttributeDefault, $request->getAttribute($customAttributeName, $customAttributeDefault));

        $request->withAttribute($customAttributeName, $customAttributeValue);

        self::assertEquals($customAttributeValue, $request->getAttribute($customAttributeName));
        self::assertEquals($customAttributeValue, $request->$customAttributeName);
    }

    public function testRequestFromGlobalsDefault(): void
    {
        $protocol = 'HTTP/1.1';
        $method = 'GET';
        $uri = '/';

        $request = Request::fromGlobals();

        self::assertEquals($_SERVER, $request->getServer());
        self::assertEquals($_POST, $request->getBody());
        self::assertEquals(new Cookie($_COOKIE), $request->getCookies());
        self::assertEquals($_GET, $request->getQuery());
        self::assertEquals($_FILES, $request->getFiles());

        self::assertEquals($uri, $request->getUri());
        self::assertEquals($protocol, $request->getProtocol());
        self::assertEquals($method, $request->getMethod());
    }

    public function testRequestFromGlobalsCustom(): void
    {
        $server = $_SERVER;

        $server['REQUEST_URI'] = '/post/12';
        $server['SERVER_PROTOCOL'] = 'HTTP/2.0';
        $server['REQUEST_METHOD'] = 'DELETE';

        $body = [
            'title' => 'Post Title',
            'text'  => 'Post text',
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

        $request = Request::fromGlobals($server, $body, $cookies, $query, $files);

        self::assertEquals($server, $request->getServer());
        self::assertEquals($server['REQUEST_URI'], $request->getUri());
        self::assertEquals($server['SERVER_PROTOCOL'], $request->getProtocol());
        self::assertEquals($server['REQUEST_METHOD'], $request->getMethod());
        self::assertEquals(new Cookie($cookies), $request->getCookies());
        self::assertEquals($query, $request->getQuery());
        self::assertEquals($body, $request->getBody());
        self::assertEquals($files, $request->getFiles());
        self::assertEquals([], $request->getAttributes());
    }
}
