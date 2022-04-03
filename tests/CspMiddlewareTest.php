<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\CSP\Middleware\CspMiddleware,
    Fyre\Middleware\MiddlewareQueue,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ServerRequest,
    PHPUnit\Framework\TestCase;

final class CspMiddlewareTest extends TestCase
{

    public function testPolicy(): void
    {
        $middleware = new CspMiddleware([
            'policy' => [
                'default-src' => 'self'
            ]
        ]);

        $queue = new MiddlewareQueue();
        $queue->add($middleware);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest;

        $response = $handler->handle($request);

        $this->assertSame(
            'default-src \'self\';',
            $response->getHeaderValue('Content-Security-Policy')
        );

        $this->assertSame(
            '',
            $response->getHeaderValue('Content-Security-Policy-Report-Only')
        );
    }

    public function testReportPolicy(): void
    {
        $middleware = new CspMiddleware([
            'report' => [
                'default-src' => 'self',
                'report-to' => 'csp-endpoint'
            ],
            'reportTo' => [
                'group' => 'csp-endpoint',
                'max_age' => '10886400',
                'endpoints' => [
                    [
                        'url' => 'https://test.com/csp-report'
                    ]
                ]
            ]
        ]);

        $queue = new MiddlewareQueue();
        $queue->add($middleware);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest;

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

}
