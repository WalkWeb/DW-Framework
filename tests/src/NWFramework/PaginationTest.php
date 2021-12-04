<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Pagination;
use Tests\AbstractTestCase;

class PaginationTest extends AbstractTestCase
{
    public function testPaginationNoPages(): void
    {
        self::assertEquals('', Pagination::getPages(10, 1, 'pages', 20));
    }

    public function testPaginationFirstPage(): void
    {
        $elements = 100;
        $page = 1;
        $url = 'pages/';
        $perPage = 10;

        self::assertEquals(
            '<span>1</span>  <a href="pages/2" title="">2</a>  <a href="pages/3" title="">3</a>  <a href="pages/4" title="">4</a> ... <a href="pages/10" title="">10</a>',
            Pagination::getPages($elements, $page, $url, $perPage)
        );
    }

    public function testPaginationLastPage(): void
    {
        $elements = 100;
        $page = 10;
        $url = 'pages/';
        $perPage = 10;

        self::assertEquals(
            '<a href="pages/" title="">1</a>  <a href="pages/7" title="">7</a>  <a href="pages/8" title="">8</a>  <a href="pages/9" title="">9</a>  <span>10</span>',
            Pagination::getPages($elements, $page, $url, $perPage)
        );
    }

    public function testPaginationMiddlePage(): void
    {
        $elements = 200;
        $page = 10;
        $url = 'pages/';
        $perPage = 10;

        // TODO Доработать пагинацию при странице по середине

        self::assertEquals(
            '<a href="pages/" title="">1</a>  <a href="pages/7" title="">7</a>  <a href="pages/8" title="">8</a>  <a href="pages/9" title="">9</a>  <span>10</span>  <a href="pages/11" title="">11</a>  <a href="pages/12" title="">12</a>  <a href="pages/13" title="">13</a> ... <a href="pages/20" title="">20</a>',
            Pagination::getPages($elements, $page, $url, $perPage)
        );
    }
}