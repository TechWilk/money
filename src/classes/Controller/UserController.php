<?php

namespace TechWilk\Money\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\EmailAddress;
use TechWilk\Money\User;
use TechWilk\Money\UserQuery;

class UserController extends AbstractController
{
    public function postUser(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Create user POST '/user'");

        $data = $request->getParsedBody();

        $data['first-name'] = trim($data['first-name']);
        $data['last-name'] = trim($data['last-name']);
        $data['email'] = new EmailAddress($data['email']);

        $u = new User();
        $u->setFirstName($data['first-name']);
        $u->setLastName($data['last-name']);
        $u->setEmail($data['email']);
        if ($data['password'] != $data['password-confirm'] || strlen($data['password']) <= 5) {
            $message = 'Passwords do not match, or are too short. Must be above 5 chars long.';

            return $this->view->render($response->withStatus(422), 'user-new.twig', ['user' => $u, 'message' => $message]);
        }
        $u->setPassword($data['password']);
        $u->save();

        return $response->withStatus(303)->withHeader('Location', $this->router->pathFor('user', ['id' => $u->getId()]));
    }

    public function getUserNew(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        return $this->view->render($response, 'user-new.twig', []);
    }

    public function postUserPassword(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Reset user password POST '/user/".$args['id']."/password'");

        $u = UserQuery::create()->findPk($args['id']);

        $data = $request->getParsedBody();

        if (!$u->checkPassword($data['old'])) {
            $message = 'Old password incorrect.';

            return $this->view->render($response->withStatus(422), 'user.twig', ['user' => $u, 'message' => $message]);
        }

        if ($data['new'] != $data['confirm'] || strlen($data['new']) <= 5) {
            $message = 'New passwords do not match, or are too short. Must be above 5 chars long.';

            return $this->view->render($response->withStatus(422), 'user.twig', ['user' => $u, 'message' => $message]);
        }

        $u->setPassword($data['new']);
        $u->save();

        $message = 'Changed successfully';

        return $this->view->render($response->withStatus(201), 'user.twig', ['user' => $u, 'message' => $message]);
    }

    public function getUser(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch user GET '/user/".$args['id']."'");
        $q = new UserQuery();
        $u = $q->findPK($args['id']);

        return $this->view->render($response, 'user.twig', ['user' => $u]);
    }
}
