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
    /**
     * New CspMiddleware constructor.
     *
     * @param ContentSecurityPolicy $csp The ContentSecurityPolicy.
     */
    public function __construct(
        protected ContentSecurityPolicy $csp
    ) {}

    /**
     * Handle a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request, Closure $next): ClientResponse
    {
        return $this->csp->addHeaders($next($request));
    }
}
