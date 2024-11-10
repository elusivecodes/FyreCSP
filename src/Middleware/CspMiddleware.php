<?php
declare(strict_types=1);

namespace Fyre\Security\Middleware;

use Closure;
use Fyre\Middleware\Middleware;
use Fyre\Security\ContentSecurityPolicy;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * CspMiddleware
 */
class CspMiddleware extends Middleware
{
    protected ContentSecurityPolicy $csp;

    /**
     * New CspMiddleware constructor.
     *
     * @param ContentSecurityPolicy $csp The ContentSecurityPolicy.
     * @param array $options Options for the middleware.
     */
    public function __construct(ContentSecurityPolicy $csp)
    {
        $this->csp = $csp;
    }

    /**
     * Process a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    public function __invoke(ServerRequest $request, Closure $next): ClientResponse
    {
        return $this->csp->addHeaders($next($request));
    }
}
