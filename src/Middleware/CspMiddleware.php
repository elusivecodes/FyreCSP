<?php
declare(strict_types=1);

namespace Fyre\Security\Middleware;

use Closure;
use Fyre\Container\Container;
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
     * @param Container $container The Container.
     * @param array $options Options for the ContentSecurityPolicy.
     */
    public function __construct(Container $container, array $options = [])
    {
        $this->csp = $container->use(ContentSecurityPolicy::class);

        foreach ($options as $key => $value) {
            switch ($key) {
                case 'reportTo':
                    $this->csp->setReportTo($value);
                    break;
                default:
                    $this->csp->createPolicy($key, $value);
                    break;
            }
        }
    }

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
