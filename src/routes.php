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
        $u->setAccountId($a->getId());
        $u->setFirstName('Christopher');
        $u->setLastName('Wilkinson');
        $u->setEmail('c@wilk.tech');
        $u->setPasswordHash(password_hash('something',PASSWORD_BCRYPT));
        $u->save();
    }
    return $this->renderer->render($response, 'index.phtml', [ "user" => $u, "router" => $this->router ] );
})->setName('user-new');


$app->get('/user/{id}', function ($request, $response, $args) {

    $this->logger->info("Fetch user GET '/user/".$args['id']."'");
    $q = new UserQuery();
    $u = $q->findPK($args['id']);
    
    return $this->renderer->render($response, 'user.phtml', [ "user" => $u, "router" => $this->router ] );
})->setName('user');


$app->get('/tags', function ($request, $response, $args) {

    $this->logger->info("Fetch tags GET '/tags'");

    $tags = HashtagQuery::create()->distinct()->orderByTag()->find();

    $lastTag = new Hashtag;
    foreach($tags as $key => $tag)
    {
        if ($tag->getTag() == $lastTag->getTag())
        {
            unset($tags[$key]);
        }
        $lastTag = $tag;
    }

    return $this->renderer->render($response, 'tags.phtml', [ "tags" => $tags, "router" => $this->router ] );
})->setName('tags');


$app->get('/transactions/tag/{tag}', function ($request, $response, $args) {

    $this->logger->info("Fetch tag GET '/tag/".$args['tag']."'");

    $transactions = TransactionQuery::create()->useHashtagQuery()->filterByTag(strtolower($args['tag']))->endUse()->orderByDate('desc')->find();

    return $this->renderer->render($response, 'transactions.phtml', [ "transactions" => $transactions, 'tagName' => $args['tag'], "router" => $this->router ] );
})->setName('tag');


// create reading
$app->post('/transaction[/{id}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Create transaction POST '/transaction'");

    $data = $request->getParsedBody();
    $transaction_data = [];
    $transaction_data['date'] = filter_var($data['date'], FILTER_SANITIZE_STRING);
    $transaction_data['value'] = filter_var($data['value'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $transaction_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $transaction_data['category'] = filter_var($data['category'][0], FILTER_SANITIZE_NUMBER_INT);
    $transaction_data['account'] = filter_var($data['account'][0], FILTER_SANITIZE_NUMBER_INT);

    $t = new Transaction();

    if ($args['id'])
    {
        $t = TransactionQuery::create()->findPK($args['id']);
    }

    $t->setDate(new DateTime($transaction_data['date']));

    if ($data['direction'] == "outgoings" )
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

    return $this->renderer->render($response, 'transaction-new.phtml', [ 'categories' => $categories, 'accounts' => $accounts, "router" => $this->router ]);
})->setName('transaction-new');

$app->get('/transaction/{id}/edit', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transaction/id/edit GET '/transaction/".$args['id']."/edit'");

    $categories = CategoryQuery::create()->find();
    $accounts = AccountQuery::create()->find();

    $q = new TransactionQuery();
    $t = $q->findPK($args['id']);

    return $this->renderer->render($response, 'transaction-new.phtml', [ "t" => $t, 'categories' => $categories, 'accounts' => $accounts, "router" => $this->router ] );
})->setName('transaction-edit');

$app->get('/transaction/{id}', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transaction GET '/transaction/".$args['id']."'");

    $q = new TransactionQuery();
    $t = $q->findPK($args['id']);

    return $this->renderer->render($response, 'transaction.phtml', [ "t" => $t, "router" => $this->router ] );
})->setName('transaction');


$app->get('/transactions[/{account}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Fetch transactions GET '/transactions");

    $transactions = TransactionQuery::create()->orderByDate('desc')->find();
    if ($args['account'])
    {
        $account = AccountQuery::create()->filterByName($args['account'])->findOne();
        $transactions = TransactionQuery::create()->filterByAccount($account)->orderByDate('desc')->find();
    }

    return $this->renderer->render($response, 'transactions.phtml', [ "transactions" => $transactions, 'account' => $account,"router" => $this->router ] );
})->setName('transactions');


$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', [ "name" => $args['name'], "router" => $this->router ] );
})->setName('home');
