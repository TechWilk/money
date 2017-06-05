<?php
// Routes

$app->post('/user', function ($request, $response, $args) {

    $this->logger->info("Create user POST '/user'");

    if (UserQuery::create()->find()->count() == 0)
    {
        $a = new Account();
        $a->setName('WILKINSON');
        $a->save();

        $u = new User();
        $u->addAccount($a);
        $u->setFirstName('Christopher');
        $u->setLastName('Wilkinson');
        $u->setEmail('c@wilk.tech');
        $u->setPasswordHash(password_hash('something',PASSWORD_BCRYPT));
        $u->save();
    }
    return $this->view->render($response, 'dashboard.twig', [ "user" => $u, ] );
})->setName('user-new-post');

$app->get('/user/new', function ($request, $response, $args) {
    
    return $this->view->render($response, 'user-new.twig', [ ] );
})->setName('user-new');


$app->post('/user/{id}/password', function ($request, $response, $args) {

    $this->logger->info("Reset user password POST '/user/".$args['id']."/password'");

    $u = UserQuery::create()->findPk($args['id']);

    $data = $request->getParsedBody();

    if (!$u->checkPassword($data['old']))
    {
        $message = 'Old password incorrect.';
        return $this->view->render($response, 'user.twig', [ "user" => $u, 'message' => $message ] );
    }

    if ($data['new'] != $data['confirm'] || strlen($data['new']) <= 5 )
    {
        $message = 'New passwords do not match, or are too short. Must be above 5 chars long.';
        return $this->view->render($response, 'user.twig', [ "user" => $u, 'message' => $message ] );
    }

    $u->setPassword($data['new']);
    $u->save();

    $message = 'Changed successfully';

    return $this->view->render($response, 'user.twig', [ "user" => $u, 'message' => $message ] );
})->setName('user-password-post');


$app->get('/user/{id}', function ($request, $response, $args) {

    $this->logger->info("Fetch user GET '/user/".$args['id']."'");
    $q = new UserQuery();
    $u = $q->findPK($args['id']);
    
    return $this->view->render($response, 'user.twig', [ "user" => $u, ] );
})->setName('user');


$app->get('/tags', function ($request, $response, $args) {

    $this->logger->info("Fetch tags GET '/tags'");

    $tags = HashtagQuery::create()->orderByTag()->find();

    $topHashtags = HashtagQuery::create()
                ->useTransactionHashtagQuery()
                    ->withColumn('COUNT(*)', 'Count')
                    ->select(array('Transaction', 'Count'))
                ->endUse()
                ->groupByTag()
                ->orderByCount('desc')
                ->limit(5)
                ;
    $newestHashtags = HashtagQuery::create()->orderById('desc')->limit(5)->find();

    return $this->view->render($response, 'tags.twig', [ "tags" => $tags, 'newest' => $newestHashtags, 'top' => $topHashtags ] );
})->setName('tags');


$app->get('/tags.json', function ($request, $response, $args) {

    $this->logger->info("Fetch tags GET '/tags'");

    $query = $request->getQueryParams('q');
    //$query = strtolower($query['q']);

    $tags = HashtagQuery::create()->orderByTag()->find();

    // if (isset($query))
    // {
    //     $tags = HashtagQuery::create()->where('Hashtag.Tag LIKE ?', '%'.$query.'%')->orderByTag()->toString();
    // }

    $tagNamesArray = [];

    foreach($tags as $tag)
    {
        $tagNamesArray[$tag->getTag()] = [$tag->getTag(), $tag->countTransactions()];  
    }

    if (isset($query['q']))
    {
        foreach($tagNamesArray as $key => $tag)
        {
            if (! (strpos($tag[0], strtolower($query['q'])) !== false) )
            {
                unset($tagNamesArray[$key]);
            }
        }
    }

    return $response->withJson($tagNamesArray);
})->setName('tags-json');


$app->get('/transactions/tag/{tag}', function ($request, $response, $args) {

    $this->logger->info("Fetch tag GET '/tag/".$args['tag']."'");

    $transactions = TransactionQuery::create()->useTransactionHashtagQuery()->useHashtagQuery()->filterByTag(strtolower($args['tag']))->endUse()->endUse()->orderByDate('desc')->find();

    return $this->view->render($response, 'transactions.twig', [ "transactions" => $transactions, 'tagName' => $args['tag'], ] );
})->setName('tag');


// create reading
$app->post('/transaction[/{id}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Create transaction POST '/transaction'");
    
    $data = $request->getParsedBody();

    $transaction_data = [];
    $transaction_data['date'] = filter_var($data['date'], FILTER_SANITIZE_STRING);
    $transaction_data['value'] = Maths::calculateString($data['value']);
    $transaction_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    //$transaction_data['category'] = filter_var($data['category'][0], FILTER_SANITIZE_NUMBER_INT);
    $transaction_data['account'] = filter_var($data['account'][0], FILTER_SANITIZE_NUMBER_INT);

    $t = new Transaction();

    if (isset($args['id']))
    {
        $t = TransactionQuery::create()->findPK($args['id']);
    }

    $t->setDate(new DateTime($transaction_data['date']));

    if (isset($data['direction']) && $data['direction'] == "outgoings" )
    {
        $transaction_data['value'] = 0 - $transaction_data['value'];
    }
    $t->setValue($transaction_data['value']);

    $t->setAccountId($transaction_data['account']);    

    $t->setDescription($transaction_data['description']);

    $t->save();

    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('transaction', [ 'id' => $t->getId() ]));
})->setName('transaction-post');

$app->get('/transaction/new', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transaction/new GET '/reading/new'");

    $categories = CategoryQuery::create()->find();
    $accounts = AccountQuery::create()->find();

    return $this->view->render($response, 'transaction-new.twig', [ 'categories' => $categories, 'accounts' => $accounts, ]);
})->setName('transaction-new');

$app->get('/transaction/{id}/edit', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transaction/id/edit GET '/transaction/".$args['id']."/edit'");

    $categories = CategoryQuery::create()->find();
    $accounts = AccountQuery::create()->find();

    $q = new TransactionQuery();
    $t = $q->findPK($args['id']);

    return $this->view->render($response, 'transaction-new.twig', [ "transaction" => $t, 'categories' => $categories, 'accounts' => $accounts, ] );
})->setName('transaction-edit');

$app->get('/transaction/{id}', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transaction GET '/transaction/".$args['id']."'");

    $q = new TransactionQuery();
    $t = $q->findPK($args['id']);

    return $this->view->render($response, 'transaction.twig', [ "transaction" => $t, ] );
})->setName('transaction');


$app->get('/transactions[/{account}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transactions GET '/transactions");

    $transactions = TransactionQuery::create()->orderByDate('desc')->find();
    if (isset($args['account']))
    {
        $account = AccountQuery::create()->filterByName($args['account'])->findOne();
        $transactions = TransactionQuery::create()->filterByAccount($account)->orderByDate('desc')->find();
        return $this->view->render($response, 'transactions.twig', [ "transactions" => $transactions, 'account' => $account, ] );
    }

    return $this->view->render($response, 'transactions.twig', [ "transactions" => $transactions, ] );
})->setName('transactions');

$app->get('/transactions/{year}/{month}', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transactions GET '/transactions/".$args['year']."/".$args['month']."/'");

    try
    {
        $minDate = new DateTime("first day of ".$args['month']." ".$args['year']);
        $maxDate = new DateTime("last day of ".$args['month']." ".$args['year']);
        $transactions = TransactionQuery::create()->filterByDate(['min' => $minDate, 'max' => $maxDate])->orderByDate('desc')->find();
    }
    catch (\Exception $e)
    {
        $transactions = [];
    }

    return $this->view->render($response, 'transactions.twig', [ "transactions" => $transactions, "date" => $args['month'].' '.$args['year'], ] );
})->setName('month');


/*
$app->get('/fixHashtags', function ($request, $response, $args) {

    $transactions = TransactionQuery::create()->find();
    foreach ($transactions as $transaction)
    {
        preg_match_all("/#(\\w+)/", $transaction->getDescription(), $hashtags);
        $hashtags = array_map('strtolower', $hashtags[1]);
        foreach ($hashtags as $tag)
        {
            var_dump($tag);
            //exit;
            $h = new Hashtag();
            if (HashtagQuery::create()->filterByTag($tag)->count() == 0)
            {
                $h->setTag($tag);
                $h->save();
               
            }
            else
            {
                $h = HashtagQuery::create()->filterByTag($tag)->findOne();
            }
            var_dump($h);
            $hasHashtag = false;
            foreach ($transaction->getHashtags() as $currentHashtag)
            {
                var_dump($currentHashtag);
                if ($currentHashtag->getTag() == $h->getTag())
                {
                    $hasHashtag = true;
                }
            }
            if (!$hasHashtag)
            {
                $transaction->addHashtag($h);
                $transaction->save();
            }
        }
    }

    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
});
*/



// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// AUTH
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


$app->get('/login', function ($request, $response, $args) {

    $this->logger->info("Fetch login GET '/login'");

    if (isset($_SESSION['userId']))
    {
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
    }

    return $this->view->render($response->withStatus(401), 'login.twig' );
})->setName('login');


$app->post('/login', function ($request, $response, $args) {

    $this->logger->info("Login POST '/login'");

    $message = "Username or password incorrect.";

    $data = $request->getParsedBody();

    try {
        $email = new EmailAddress($data['username']);
    } catch (InvalidArgumentException $e) {
        return $this->view->render($response->withStatus(401), 'login.twig', ['message' => $message] );
    }
    $password = filter_var($data['password'], FILTER_SANITIZE_STRING);

    if ($email == "" || $password == "")
    {
        return $this->view->render($response->withStatus(401), 'login.twig', ['message' => $message] );
    }

    // login
    $auth = new Authentication($this);
    try
    {
        if ($auth->loginAttempt($email, $password))
        {
            if (isset($_SESSION['urlRedirect']))
            {
                $url = $_SESSION['urlRedirect'];
                unset($_SESSION['urlRedirect']);
                return $response->withStatus(303)->withHeader('Location', $url);
            }
            return $response->withStatus(303)->withHeader('Location', $this->router->pathFor('home'));
        }
    }
    catch (\Exception $e)
    {
        $message = "Too many failed login attempts. Please try again in 15 minutes.";
    }
    return $this->view->render($response->withStatus(401), 'login.twig', ['username' => $email, 'message' => $message ] );
})->setName('login-post');


$app->get('/logout', function ($request, $response, $args) {

    $this->logger->info("Fetch logout GET '/logout'");

    unset($_SESSION['userId']);

    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
})->setName('logout');




$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("GET dashboard '/' route");

    $topHashtags = HashtagQuery::create()
                    ->useTransactionHashtagQuery()
                        ->withColumn('COUNT(*)', 'Count')
                        ->select(array('Transaction', 'Count'))
                    ->endUse()
                    ->groupByTag()
                    ->orderByCount('desc')
                    ->limit(5)
                    ;
    $newestHashtags = HashtagQuery::create()->orderById('desc')->limit(5)->find();

    $hashtags = [
        'top' => $topHashtags,
        'newest' => $newestHashtags,
    ];

    $yearTransactions = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of January this year"), 'max' => new DateTime("last day of December this year")])->orderByDate('desc')->find();
    $lastYearTransactions = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of January last year"), 'max' => new DateTime("last day of December last year")])->orderByDate('desc')->find();

    // this year
    $thisYearIncoming = 0;
    $thisYearOutgoing = 0;

    foreach ($yearTransactions as $t)
    {
        if ($t->getValue() > 0)
        {
            $thisYearIncoming += $t->getValue();
        }
        else
        {
            $thisYearOutgoing += abs($t->getValue());
        }
    }

    // last year
    $lastYearIncoming = 0;
    $lastYearOutgoing = 0;

    foreach ($lastYearTransactions as $t)
    {
        if ($t->getValue() > 0)
        {
            $lastYearIncoming += $t->getValue();
        }
        else
        {
            $lastYearOutgoing += abs($t->getValue());
        }
    }

    $months = [];
    foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
    {
        $transactionsInMonth = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of $month this year"), 'max' => new DateTime("last day of $month this year")])->orderByDate('desc')->find();
        if ($transactionsInMonth->count() != 0)
        {
            $months[$month]['transactions'] = $transactionsInMonth;
            $months[$month]['income'] = 0;
            $months[$month]['outgoings'] = 0;
            foreach ($transactionsInMonth as $t)
            {
                if ($t->getValue() > 0)
                {
                    $months[$month]['income'] += $t->getValue();
                }
                else
                {
                    $months[$month]['outgoings'] += abs($t->getValue());
                }
            }
        }
    }

    $monthsLastYear = [];
    foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
    {
        $transactionsInMonth = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of $month last year"), 'max' => new DateTime("last day of $month last year")])->orderByDate('desc')->find();
        if ($transactionsInMonth->count() != 0)
        {
            $monthsLastYear[$month]['transactions'] = $transactionsInMonth;
            $monthsLastYear[$month]['income'] = 0;
            $monthsLastYear[$month]['outgoings'] = 0;
            foreach ($transactionsInMonth as $t)
            {
                if ($t->getValue() > 0)
                {
                    $monthsLastYear[$month]['income'] += $t->getValue();
                }
                else
                {
                    $monthsLastYear[$month]['outgoings'] += abs($t->getValue());
                }
            }
        }
    }

    $years = [
        'last' => [
            'income' => $lastYearIncoming,
            'outgoings' => $lastYearOutgoing,
            'months' => $monthsLastYear,
        ],
        'this' => [
            'income' => $thisYearIncoming,
            'outgoings' => $thisYearOutgoing,
            'months' => $months,
        ],
    ];

    $monthTransactions = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of this month"), 'max' => new DateTime("last day of this month")])->orderByDate('desc')->find();
    $lastMonthTransactions = TransactionQuery::create()->filterByDate(['min' => new DateTime("first day of last month"), 'max' => new DateTime("last day of last month")])->orderByDate('desc')->find();

    // this month
    $thisMonthIncoming = 0;
    $thisMonthOutgoing = 0;

    foreach ($monthTransactions as $t)
    {
        if ($t->getValue() > 0)
        {
            $thisMonthIncoming += $t->getValue();
        }
        else
        {
            $thisMonthOutgoing += abs($t->getValue());
        }
    }

    // last month
    $lastMonthIncoming = 0;
    $lastMonthOutgoing = 0;

    foreach ($lastMonthTransactions as $t)
    {
        if ($t->getValue() > 0)
        {
            $lastMonthIncoming += $t->getValue();
        }
        else
        {
            $lastMonthOutgoing += abs($t->getValue());
        }
    }

    $months = [
        'last' => [
            'income' => $lastMonthIncoming,
            'outgoings' => $lastMonthOutgoing,
            'transactions' => $lastMonthTransactions,
        ],
        'this' => [
            'income' => $thisMonthIncoming,
            'outgoings' => $thisMonthOutgoing,
            'transactions' => $monthTransactions,
        ],
    ];


    // Render index view
    return $this->view->render($response, 'dashboard.twig', [ 'hashtags' => $hashtags, 'years' => $years, 'months' => $months ] );
})->setName('home');