<?php

namespace TechWilk\Money\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\Account;
use TechWilk\Money\UserAccount;
use TechWilk\Money\UserAccountQuery;

class AccountController extends AbstractController
{
    public function postAccount(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Create account POST '/account'");

        $data = $request->getParsedBody();

        if (empty($data['name'])) {
            $message = 'Invalid account name: cannot be empty.';

            return $this->view->render($response, 'account-new.twig', [
                'message' => $message,
            ]);
        }

        // does this user already have an account with this name?
        $alreadyExists = UserAccountQuery::create()->filterByAlias($data['name'])->count();
        if ($alreadyExists > 0) {
            $message = 'Invalid account name: account already exists with this name.';

            return $this->view->render($response, 'account-new.twig', [
                'message' => $message,
            ]);
        }

        $account = new Account();
        $account->save();

        $userAccount = new UserAccount();
        $userAccount->setAccount($account);
        $userAccount->setUser($user);
        $userAccount->setAlias($data['name']);
        $userAccount->save();

        return $response->withStatus(303)->withHeader('Location', $this->router->pathFor('user', ['id' => $user->getId()]));
    }

    public function getAccountNew(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        return $this->view->render($response, 'account-new.twig', []);
    }
}
