<?php

namespace TechWilk\Money;

use Aptoma\Twig\Extension\MarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;
use Monolog;
use Slim;
use TechWilk\Twig\Extension\Hashtagify;
use TechWilk\Twig\Extension\HashtagifyUrlGenerator;
use Twig_Extensions_Extension_Date;

// DIC configuration

$container = $app->getContainer();

// TWIG view renderer
$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Slim\Views\Twig($settings['template_path'], [
        'cache' => false, // or 'path/to/cache'
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    $engine = new MarkdownEngine\MichelfMarkdownEngine();
    $view->addExtension(new MarkdownExtension($engine));

    $view->addExtension(new \DPolac\TwigLambda\LambdaExtension());
    $view->addExtension(new Twig_Extensions_Extension_Date());

    $urlGenerator = new HashtagifyUrlGenerator\SlimHashtagifyUrlGenerator($c['router'], 'tag', 'tag');
    $view->addExtension(new Hashtagify($urlGenerator));

    $env = $view->getEnvironment();
    $env->getExtension('Twig_Extension_Core')->setNumberFormat(2, '.', ',');
    //$env->addGlobal('site', new Site);
    $env->addGlobal('currenturl', $c->get('request')->getUri());
    $env->addGlobal('currentpath', $c->get('request')->getUri()->getBasePath().'/'.$c->get('request')->getUri()->getPath());

    if (isset($_SESSION['userId'])) {
        $u = UserQuery::create()->findPk($_SESSION['userId']);
        if (isset($u)) {
            $env->addGlobal('currentuser', $u);
        }
    }

    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

$container['auth'] = function ($c) {
    $authProvider = new AuthProvider\UsernamePassword\UsernamePasswordAuth();

    $allowedRoutes = [
        'login',
        'login-post',
        'login-auth',
        'login-callback',
        'sign-up',
        'sign-up-post',
        'sign-up-cancel',
        'user-calendar',
        'job-daily',
        'install',
        'install-database',
        'install-user',
        'install-user-post',
    ];

    return new Authentication($c, $authProvider, $allowedRoutes);
};
