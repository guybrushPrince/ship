<?php

namespace ship\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

/**
 * Abstract Class SettingsMiddleware.
 *
 * <p>A <em>Ship</em> middleware related to a Slim middleware. This middleware is used to set user specific
 * settings.</p>
 *
 * @package ship
 * @subpackge ship/middlewares
 *
 * @author Dr. Dipl.-Inf. Thomas M. Prinz
 * @version 1.3.0
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