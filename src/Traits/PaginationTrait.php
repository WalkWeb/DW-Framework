<?php

namespace WalkWeb\NW\Traits;

trait PaginationTrait
{
    /**
     * Собирает и возвращает html-контент для отображения пагинации на странице
     *
     * @param int $elements
     * @param int $page
     * @param string $url
     * @param int $perPage
     * @return string
     */
    public function getPages(int $elements, int $page, string $url, int $perPage = 5): string
    {
        $numberPage = (int)ceil($elements / $perPage);
        $content = '';
        $left = '';
        $right = '';

        if ($numberPage === 1) {
            return $content;
        }

        $i = 1;
        while ($i <= $numberPage) {
            if ($i === $page) {
                $content .= ' <span>' . $i . '</span> ';
            } else {
                if ($i < ($page - 3)) {
                    $left = ' <a href="' . $url . '" title="">1</a> ' . (($page - 4 <= $numberPage) ? '...' : '');
                } elseif ($i > ($page + 3)) {
                    $right = (($page + 4 >= $numberPage) ? '' : '...') . ' <a href="' . $url . $numberPage . '" title="">' . $numberPage . '</a> ';
                } else {
                    $content .= ' <a href="' . $url . $i . '" title="">' . $i . '</a> ';
                }
            }
            $i++;
        }

        return trim($left . $content . $right);
    }
}
