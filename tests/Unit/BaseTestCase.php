<?php

namespace TechWilk\Money\Tests\Unit;

use Slim\App;
use TechWilk\Money\Tests\Data\Database;
use TechWilk\Money\Tests\Data\TestData;

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/London');

new Database(new TestData());

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit\Framework\TestCase
{
}
