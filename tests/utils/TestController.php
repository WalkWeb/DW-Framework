<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\Controller;

/**
 * Так как NW\Controller является абстрактным классом, мы не можем протестировать его напрямую. Для этого создан
 * TestController, который просто наследуется от NW\Controller
 *
 * @package Tests\utils
 */
class TestController extends Controller
{

}
