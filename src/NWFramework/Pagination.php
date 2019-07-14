<?php

namespace NW;

/**
 * Class Pagination
 *
 * Принимает:
 * - Количество элементов в списке
 * - Текущая страница
 * - URL ссылки
 * - При необходимости: количество элементов на страницу
 *
 * Внутренние параметы (могут быть перезаписаны):
 * - Количество элементов на страницу
 *
 * Выдает:
 * - блок навигации по страницам
 */
class Pagination
{
    private $perPage = 10;

    public function getPages($elements, $page, $url, $perPage = null)
    {
        if ($perPage) $this->perPage = $perPage;
        $numberPage = (int) ceil($elements/$this->perPage);
        $content = '';
        $left = '';
        $right = '';

        if ($numberPage === 1) return $content;

        $i = 1;
        while ($i <= $numberPage) {
            if ($i === $page) {
                $content .= ' ' . $i . ' ';
            } else {
                if ($i < ($page - 3)) {
                    $left = ' <a href="' . $url . '1" title="">1</a> ...';

                    // TODO Доработать вариант нумерации 3 4 5 ... 6
//                    if ($i !== 1) {
//                        $left = ' <a href="' . $url . '1" title="">1</a> ...';
//                    } else {
//                        $left = ' <a href="' . $url . '1" title="">1</a> ';
//                    }
                }
                elseif ($i > ($page + 3)) {
                    $right = '... <a href="' . $url . $numberPage . '" title="">' . $numberPage . '</a> ';

                    // TODO Доработать вариант нумерации 1 ... 2 3 4
//                    if ($i !== $numberPage) {
//                        $right = '... <a href="' . $url . $numberPage . '" title="">' . $numberPage . '</a> ';
//                    } else {
//                        $right = ' <a href="' . $url . $numberPage . '" title="">' . $numberPage . '</a> ';
//                    }
                } else {
                    $content .= ' <a href="' . $url . $i . '" title="">' . $i . '</a> ';
                }
            }
            $i++;
        }

        return $left . $content . $right;
    }
}
