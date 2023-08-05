<?php
declare(strict_types=1);

namespace Fyre\Security\Middleware;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\RequestHandler;
use Fyre\Security\CspBuilder;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_replace;

/**
 * CspMiddleware
 */
class CspMiddleware extends Middleware
{

    protected static array $defaults = [
        'default' => [],
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
        $options = array_replace(static::$defaults, $options);

        if ($options['default'] !== null) {
            CspBuilder::createPolicy(CspBuilder::DEFAULT, $options['default']);
        }

        if ($options['report'] !== null) {
            CspBuilder::createPolicy(CspBuilder::REPORT, $options['report']);
        }

        CspBuilder::setReportTo($options['reportTo']);
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

        return CspBuilder::addHeaders($response);
    }

}
