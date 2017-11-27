<?php

namespace TechWilk\Money\Controller;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\EmailAddress;
use TechWilk\Money\Exception\UnknownUserException;
use TechWilk\Money\HashtagQuery;
use TechWilk\Money\TransactionQuery;
use DateTime;

class DashboardController extends AbstractController
{
    public function getMainDashboard(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("GET dashboard '/' route");
        
        $topHashtags = HashtagQuery::create()
                        ->useTransactionHashtagQuery()
                            ->withColumn('COUNT(*)', 'Count')
                            ->select(['Transaction', 'Count'])
                        ->endUse()
                        ->groupByTag()
                        ->orderByCount('desc')
                        ->limit(5);
        $newestHashtags = HashtagQuery::create()->orderById('desc')->limit(5)->find();
    
        $hashtags = [
            'top'    => $topHashtags,
            'newest' => $newestHashtags,
        ];
    
        $yearTransactions = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime('first day of January this year'), 'max' => new DateTime('last day of December this year')])->orderByDate('desc')->find();
        $lastYearTransactions = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime('first day of January last year'), 'max' => new DateTime('last day of December last year')])->orderByDate('desc')->find();
    
        // this year
        $thisYearIncoming = 0;
        $thisYearOutgoing = 0;
    
        foreach ($yearTransactions as $t) {
            if ($t->getValue() > 0) {
                $thisYearIncoming += $t->getValue();
            } else {
                $thisYearOutgoing += abs($t->getValue());
            }
        }
    
        // last year
        $lastYearIncoming = 0;
        $lastYearOutgoing = 0;
    
        foreach ($lastYearTransactions as $t) {
            if ($t->getValue() > 0) {
                $lastYearIncoming += $t->getValue();
            } else {
                $lastYearOutgoing += abs($t->getValue());
            }
        }
    
        $months = [];
        foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month) {
            $transactionsInMonth = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime("first day of $month this year"), 'max' => new DateTime("last day of $month this year")])->orderByDate('desc')->find();
            if ($transactionsInMonth->count() != 0) {
                $months[$month]['transactions'] = $transactionsInMonth;
                $months[$month]['income'] = 0;
                $months[$month]['outgoings'] = 0;
                foreach ($transactionsInMonth as $t) {
                    if ($t->getValue() > 0) {
                        $months[$month]['income'] += $t->getValue();
                    } else {
                        $months[$month]['outgoings'] += abs($t->getValue());
                    }
                }
            }
        }
    
        $monthsLastYear = [];
        foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month) {
            $transactionsInMonth = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime("first day of $month last year"), 'max' => new DateTime("last day of $month last year")])->orderByDate('desc')->find();
            if ($transactionsInMonth->count() != 0) {
                $monthsLastYear[$month]['transactions'] = $transactionsInMonth;
                $monthsLastYear[$month]['income'] = 0;
                $monthsLastYear[$month]['outgoings'] = 0;
                foreach ($transactionsInMonth as $t) {
                    if ($t->getValue() > 0) {
                        $monthsLastYear[$month]['income'] += $t->getValue();
                    } else {
                        $monthsLastYear[$month]['outgoings'] += abs($t->getValue());
                    }
                }
            }
        }
    
        $years = [
            'last' => [
                'income'    => $lastYearIncoming,
                'outgoings' => $lastYearOutgoing,
                'months'    => $monthsLastYear,
            ],
            'this' => [
                'income'    => $thisYearIncoming,
                'outgoings' => $thisYearOutgoing,
                'months'    => $months,
            ],
        ];
    
        $monthTransactions = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime('first day of this month'), 'max' => new DateTime('last day of this month')])->orderByDate('desc')->find();
        $lastMonthTransactions = TransactionQuery::create()->forUser($this->auth->currentUser())->filterByDate(['min' => new DateTime('first day of last month'), 'max' => new DateTime('last day of last month')])->orderByDate('desc')->find();
    
        // this month
        $thisMonthIncoming = 0;
        $thisMonthOutgoing = 0;
    
        foreach ($monthTransactions as $t) {
            if ($t->getValue() > 0) {
                $thisMonthIncoming += $t->getValue();
            } else {
                $thisMonthOutgoing += abs($t->getValue());
            }
        }
    
        // last month
        $lastMonthIncoming = 0;
        $lastMonthOutgoing = 0;
    
        foreach ($lastMonthTransactions as $t) {
            if ($t->getValue() > 0) {
                $lastMonthIncoming += $t->getValue();
            } else {
                $lastMonthOutgoing += abs($t->getValue());
            }
        }
    
        $months = [
            'last' => [
                'income'       => $lastMonthIncoming,
                'outgoings'    => $lastMonthOutgoing,
                'transactions' => $lastMonthTransactions,
            ],
            'this' => [
                'income'       => $thisMonthIncoming,
                'outgoings'    => $thisMonthOutgoing,
                'transactions' => $monthTransactions,
            ],
        ];
    
        // Render index view
        return $this->view->render($response, 'dashboard.twig', ['hashtags' => $hashtags, 'years' => $years, 'months' => $months]);
    }
}