<?php

namespace TechWilk\Money;

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new Authentication($app->getContainer()));
