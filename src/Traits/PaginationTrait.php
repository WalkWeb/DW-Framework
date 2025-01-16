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
     * @param string $onClick
     * @return string
     */
    public function getPages(
        int $elements,
        int $page,
        string $url,
        int $perPage = 5,
        string $onClick = ''
    ): string
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
                    $link = $onClick ? ' <span onclick="' . $onClick . '(1)" class="link">1</span> ' : ' <a href="' . $url . '" title="">1</a> ';
                    $left = $link . (($page - 4 <= $numberPage) ? '...' : '');
                } elseif ($i > ($page + 3)) {
                    $link = $onClick ? ' <span onclick="' . $onClick . '(' . $numberPage . ')" class="link">' . $numberPage . '</span> ' : ' <a href="' . $url . $numberPage . '" title="">' . $numberPage . '</a> ';
                    $right = (($page + 4 >= $numberPage) ? '' : '...') . $link;
                } else {
                    $link = $onClick ? ' <span onclick="' . $onClick . '(' . $i . ')" class="link">' . $i . '</span> ' : ' <a href="' . $url . $i . '" title="">' . $i . '</a> ';
                    $content .= $link;
                }
            }
            $i++;
        }

        return trim($left . $content . $right);
    }
}
