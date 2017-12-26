<?php

namespace TechWilk\Money\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use TechWilk\Money\Authentication;

abstract class AbstractController
{
    protected $view;
    protected $logger;
    protected $auth;
    protected $router;

    public function __construct(ContainerInterface $container)
    {
        $this->setupInstanceVariables(
            $container->view,
            $container->logger,
            $container->auth,
            $container->router
        );
    }

    protected function setupInstanceVariables(Twig $view, Logger $logger, Authentication $auth, RouterInterface $router)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->auth = $auth;
        $this->router = $router;
    }

    public function groupTransactionsByMonth($transactions)
    {
        $months = [];

        foreach ($transactions as $transaction) {
            $month = $transaction->getDate('Y-m');

            // fix undefined
            if (empty($months[$month]['income'])) {
                $months[$month]['income'] = 0;
            }

            if (empty($months[$month]['outgoings'])) {
                $months[$month]['outgoings'] = 0;
            }

            // actual calculations
            $months[$month]['transactions'][] = $transaction;
            if ($transaction->getValue() > 0) {
                $months[$month]['income'] += $transaction->getValue();
            } else {
                $months[$month]['outgoings'] += abs($transaction->getValue());
            }
        }

        return $months;
    }
}
