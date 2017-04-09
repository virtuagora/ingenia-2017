<?php
namespace App\Service;

use Adbar\Session;
use RuntimeException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CSRFService
{
    protected $session;
    protected $tokenError;
    protected $errorMessage = 'Invalid security token.';

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Check if session is started
        if (!$this->session->isActive()) {
             throw new RuntimeException('CSRF middleware failed. Session is not started.');
        }
        // Validate token
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = (array)$request->getParsedBody();
            $token = isset($body['csrf_token']) ? $body['csrf_token'] : false;
            if ($token === false || $this->validateToken($token) === false) {
                // Generate new token
                $request = $this->generateToken($request);
                // Create token error
                $tokenError = $this->getTokenError();
                return $tokenError($request, $response, $next);
            }
        }
        // Generate new token
        if (!$this->session->hasIn('csrf', 'token')) {
            $request = $this->generateToken($request);
        }
        return $next($request, $response);
    }

    protected function validateToken($token)
    {
        return is_string($token) && $token === $this->session->getFrom('csrf', 'token', null);
    }

    protected function generateToken(Request $request)
    {
        $token = bin2hex(random_bytes(16));
        $this->session->setTo('csrf', 'token', $token);
        $request = $request->withAttribute('csrf_token', $token);
        return $request;
    }

    protected function getTokenError()
    {
        if (empty($this->tokenError)) {
            $this->tokenError = function (Request $request, Response $response, callable $next) {
                $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
                $body->write($this->errorMessage);
                return $response->withStatus(400)->withHeader('Content-type', 'text/plain')->withBody($body);
            };
        }
        return $this->tokenError;
    }

    public function setTokenError($tokenError)
    {
        $this->tokenError = $tokenError;
    }

    public function setTokenErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}
