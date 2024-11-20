<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Security\Middleware\CspMiddleware;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;

final class CspMiddlewareTest extends TestCase
{
    protected Config $config;

    protected Container $container;

    public function testPolicy(): void
    {
        $this->config->set('Csp', [
            'default' => [
                'default-src' => 'self',
            ],
        ]);
        $middleware = $this->container->build(CspMiddleware::class);

        $queue = new MiddlewareQueue();
        $queue->add($middleware);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class);

        $response = $handler->handle($request);

        $this->assertSame(
            'default-src \'self\';',
            $response->getHeaderValue('Content-Security-Policy')
        );

        $this->assertNull(
            $response->getHeaderValue('Content-Security-Policy-Report-Only')
        );
    }

    public function testReportPolicy(): void
    {
        $this->config->set('Csp', [
            'report' => [
                'default-src' => 'self',
                'report-to' => 'csp-endpoint',
            ],
            'reportTo' => [
                'group' => 'csp-endpoint',
                'max_age' => '10886400',
                'endpoints' => [
                    [
                        'url' => 'https://test.com/csp-report',
                    ],
                ],
            ],
        ]);
        $middleware = $this->container->build(CspMiddleware::class);

        $queue = new MiddlewareQueue();
        $queue->add($middleware);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class);

        $response = $handler->handle($request);

        $this->assertSame(
            'default-src \'self\'; report-to csp-endpoint;',
            $response->getHeaderValue('Content-Security-Policy-Report-Only')
        );

        $this->assertSame(
            '{"group":"csp-endpoint","max_age":"10886400","endpoints":[{"url":"https://test.com/csp-report"}]}',
            $response->getHeaderValue('Report-To')
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(Config::class);

        $this->config = $this->container->use(Config::class);
    }
}
