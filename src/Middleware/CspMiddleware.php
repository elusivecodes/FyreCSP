<?php
declare(strict_types=1);

namespace Fyre\CSP\Middleware;

use
    Fyre\CSP\CspBuilder,
    Fyre\Middleware\Middleware,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

use const
    JSON_UNESCAPED_SLASHES;

use function
    array_replace_recursive,
    json_encode;

/**
 * CspMiddleware
 */
class CspMiddleware extends Middleware
{

    protected static array $defaults = [
        'policy' => [],
        'report' => null,
        'reportTo' => []
    ];

    protected array $reportTo = [];

    /**
     * New CspMiddleware constructor.
     * @param array $options Options for the middleware.
     */
    public function __construct(array $options = [])
    {
        $options = array_replace_recursive(static::$defaults, $options);

        if ($options['policy'] !== null) {
            CspBuilder::create('policy', $options['policy']);
        }

        if ($options['report'] !== null) {
            CspBuilder::create('report', $options['report']);
        }

        $this->reportTo = $options['reportTo'];
    }

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        $response = $handler->handle($request);

        CspBuilder::addHeaders($response);

        if ($this->reportTo !== []) {
            $response->setHeader('Report-To', json_encode($this->reportTo, JSON_UNESCAPED_SLASHES));
        }

        return $response;
    }

}
