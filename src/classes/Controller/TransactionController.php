<?php

namespace TechWilk\Money\Controller;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\EmailAddress;
use TechWilk\Money\Exception\UnknownUserException;

class TransactionController extends AbstractController
{
    public function postTransaction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Create transaction POST '/transaction'");
        
        $data = $request->getParsedBody();
    
        $transaction_data = [];
        $transaction_data['date'] = filter_var($data['date'], FILTER_SANITIZE_STRING);
        $transaction_data['value'] = Maths::calculateString($data['value']);
        $transaction_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        //$transaction_data['category'] = filter_var($data['category'][0], FILTER_SANITIZE_NUMBER_INT);
        $transaction_data['account'] = filter_var($data['account'][0], FILTER_SANITIZE_NUMBER_INT);
    
        $t = new Transaction();
    
        if (isset($args['id'])) {
            $t = TransactionQuery::create()->forCurrentUser($this)->findPK($args['id']);
        }
    
        $t->setDate(new DateTime($transaction_data['date']));
    
        if (isset($data['direction']) && $data['direction'] == 'outgoings') {
            $transaction_data['value'] = 0 - $transaction_data['value'];
        }

        $t->setValue($transaction_data['value']);
        $t->setAccountId($transaction_data['account']);
        $t->setDescription($transaction_data['description']);
        $t->save();
    
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('transaction', ['id' => $t->getId()]));
    }

    public function getTransactionNewForm(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch transaction/new GET '/reading/new'");
        
        $categories = CategoryQuery::create()->find();
        $accounts = AccountQuery::create()->filterByCurrentUser($this)->find();
    
        return $this->view->render($response, 'transaction-new.twig', ['categories' => $categories, 'accounts' => $accounts]);
    }

    public function getTransactionEditForm(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch transaction/id/edit GET '/transaction/".$args['id']."/edit'");
        
        $categories = CategoryQuery::create()->find();
        $accounts = AccountQuery::create()->find();
    
        $t = TransactionQuery::create()->forCurrentUser($this)->findPK($args['id']);
    
        return $this->view->render($response, 'transaction-new.twig', ['transaction' => $t, 'categories' => $categories, 'accounts' => $accounts]);
    }

    public function getTransaction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch transaction GET '/transaction/".$args['id']."'");
    
        $t = TransactionQuery::create()->forCurrentUser($this)->findPK($args['id']);
    
        return $this->view->render($response, 'transaction.twig', ['transaction' => $t]);
    }

    public function getTransactionsForAccount(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch transactions GET '/transactions");
        
        $transactions = TransactionQuery::create()->forCurrentUser($this)->orderByDate('desc')->find();
        if (isset($args['account'])) {
            $account = AccountQuery::create()->filterByCurrentUser($this)->filterByName($args['account'])->findOne();
            $transactions = TransactionQuery::create()->forCurrentUser($this)->filterByAccount($account)->orderByDate('desc')->find();
    
            return $this->view->render($response, 'transactions.twig', ['transactions' => $transactions, 'account' => $account]);
        }
    
        return $this->view->render($response, 'transactions.twig', ['transactions' => $transactions]);
    }

    public function getTransactionsForMonth(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch transactions GET '/transactions/".$args['year'].'/'.$args['month']."/'");
        
        try {
            $minDate = new DateTime('first day of '.$args['month'].' '.$args['year']);
            $maxDate = new DateTime('last day of '.$args['month'].' '.$args['year']);
            $transactions = TransactionQuery::create()->forCurrentUser($this)->filterByDate(['min' => $minDate, 'max' => $maxDate])->orderByDate('desc')->find();
        } catch (\Exception $e) {
            $transactions = [];
        }
    
        return $this->view->render($response, 'transactions.twig', ['transactions' => $transactions, 'date' => $args['month'].' '.$args['year']]);
    }
}