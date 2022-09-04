<?php

declare(strict_types=1);

namespace Tests\src\NWFramework\Request;

use NW\Request\Request;
use Tests\AbstractTestCase;

class RequestTest extends AbstractTestCase
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
        self::assertEquals([], $request->getCookies());
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
        self::assertEquals($cookies, $request->getCookies());
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
}
