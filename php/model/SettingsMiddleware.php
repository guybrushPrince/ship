<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

/**
 * Class SettingsMiddleware.
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 */
abstract class SettingsMiddleware {

    /**
     * The user.
     * @var string|null
     */
    private ?string $user = null;

    // @Override
    public function __invoke(Request $request, RequestHandler $handler) : ResponseInterface {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $user = $route->getArgument('user');
        $this->user = $user;
        return $this->invoke($request, $handler);
    }

    /**
     * The actual invocation of the middleware.
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public abstract function invoke(Request $request, RequestHandler $handler) : ResponseInterface;

    /**
     * Get the user.
     * @return string|null
     */
    public function getUser() : ?string {
        return $this->user;
    }
}
?>