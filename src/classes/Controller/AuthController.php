<?php

namespace TechWilk\Money\Controller;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TechWilk\Money\EmailAddress;
use TechWilk\Money\Exception\UnknownUserException;
use TechWilk\Money\Authentication;

class AuthController extends AbstractController
{
    public function getLogin(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch login GET '/login'");
        
        if (isset($_SESSION['userId'])) {
            return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
        }
    
        return $this->view->render($response->withStatus(401), 'login.twig');
    }

    public function postLogin(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Login POST '/login'");
    
        $message = 'Username or password incorrect.';
    
        $data = $request->getParsedBody();
    
        try {
            $email = new EmailAddress($data['username']);
        } catch (\InvalidArgumentException $e) {
            return $this->view->render($response->withStatus(401), 'login.twig', ['message' => $message]);
        }
        $password = filter_var($data['password'], FILTER_SANITIZE_STRING);
    
        if ($email == '' || $password == '') {
            return $this->view->render($response->withStatus(401), 'login.twig', ['message' => $message]);
        }
    
        // login
        try {
            if ($this->auth->loginAttempt($email, $password)) {
                if (isset($_SESSION['urlRedirect'])) {
                    $url = $_SESSION['urlRedirect'];
                    unset($_SESSION['urlRedirect']);
    
                    return $response->withStatus(303)->withHeader('Location', $url);
                }
    
                return $response->withStatus(303)->withHeader('Location', $this->router->pathFor('home'));
            }
        } catch (\Exception $e) {
            $message = 'Too many failed login attempts. Please try again in 15 minutes.';
        }
    
        return $this->view->render($response->withStatus(401), 'login.twig', ['username' => $email, 'message' => $message]);
    }

    public function getLogout(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Fetch logout GET '/logout'");
    
        unset($_SESSION['userId']);
    
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
    }
}