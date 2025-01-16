<?php

declare(strict_types=1);

namespace Tests\src\Traits;

use WalkWeb\NW\Traits\PaginationTrait;
use Tests\AbstractTest;

class PaginationTraitTest extends AbstractTest
{
    use PaginationTrait;

    public function testPaginationNoPages(): void
    {
        self::assertEquals('', $this->getPages(10, 1, 'pages', 20));
    }

    public function testPaginationFirstPageDefault(): void
    {
        $elements = 100;
        $page = 1;
        $url = 'pages/';
        $perPage = 10;

        self::assertEquals(
            '<span>1</span>  <a href="pages/2" title="">2</a>  <a href="pages/3" title="">3</a>  <a href="pages/4" title="">4</a> ... <a href="pages/10" title="">10</a>',
            $this->getPages($elements, $page, $url, $perPage)
        );
    }

    public function testPaginationFirstPageOnClick(): void
    {
        $elements = 100;
        $page = 1;
        $url = '';
        $perPage = 10;
        $onclick = 'getComments';

        self::assertEquals(
            '<span>1</span>  <span onclick="getComments(2)" class="link">2</span>  <span onclick="getComments(3)" class="link">3</span>  <span onclick="getComments(4)" class="link">4</span> ... <span onclick="getComments(10)" class="link">10</span>',
            $this->getPages($elements, $page, $url, $perPage, $onclick)
        );
    }

    public function testPaginationLastPageDefault(): void
    {
        $elements = 100;
        $page = 10;
        $url = 'pages/';
        $perPage = 10;

        self::assertEquals(
            '<a href="pages/" title="">1</a> ... <a href="pages/7" title="">7</a>  <a href="pages/8" title="">8</a>  <a href="pages/9" title="">9</a>  <span>10</span>',
            $this->getPages($elements, $page, $url, $perPage)
        );
    }

    public function testPaginationLastPageOnClick(): void
    {
        $elements = 100;
        $page = 10;
        $url = '';
        $perPage = 10;
        $onclick = 'getComments';

        self::assertEquals(
            '<span onclick="getComments(1)" class="link">1</span> ... <span onclick="getComments(7)" class="link">7</span>  <span onclick="getComments(8)" class="link">8</span>  <span onclick="getComments(9)" class="link">9</span>  <span>10</span>',
            $this->getPages($elements, $page, $url, $perPage, $onclick)
        );
    }

    public function testPaginationMiddlePageDefault(): void
    {
        $elements = 200;
        $page = 10;
        $url = 'pages/';
        $perPage = 10;

        self::assertEquals(
            '<a href="pages/" title="">1</a> ... <a href="pages/7" title="">7</a>  <a href="pages/8" title="">8</a>  <a href="pages/9" title="">9</a>  <span>10</span>  <a href="pages/11" title="">11</a>  <a href="pages/12" title="">12</a>  <a href="pages/13" title="">13</a> ... <a href="pages/20" title="">20</a>',
            $this->getPages($elements, $page, $url, $perPage)
        );
    }

    public function testPaginationMiddlePageOnClick(): void
    {
        $elements = 200;
        $page = 10;
        $url = '';
        $perPage = 10;
        $onclick = 'getComments';

        self::assertEquals(
            '<span onclick="getComments(1)" class="link">1</span> ... <span onclick="getComments(7)" class="link">7</span>  <span onclick="getComments(8)" class="link">8</span>  <span onclick="getComments(9)" class="link">9</span>  <span>10</span>  <span onclick="getComments(11)" class="link">11</span>  <span onclick="getComments(12)" class="link">12</span>  <span onclick="getComments(13)" class="link">13</span> ... <span onclick="getComments(20)" class="link">20</span>',
            $this->getPages($elements, $page, $url, $perPage, $onclick)
        );
    }
}
