<?php

namespace Charcoal\Tests\Cache\Middleware;

// From 'tedivm/stash'
use Stash\Pool;

// From 'charcoal-cache'
use Charcoal\Cache\CacheConfig;
use Charcoal\Cache\Middleware\CacheMiddleware;

/**
 * Test HTTP Requests with CacheMiddleware.
 *
 * @coversDefaultClass \Charcoal\Cache\Middleware\CacheMiddleware
 */
class CacheMiddlewareRequestTest extends AbstractCacheMiddlewareTest
{
    /**
     * Prepare the cache pool.
     *
     * @return void
     */
    public function setUp()
    {
        static::createCachePool();
    }

    /**
     * Empty the cache pool.
     *
     * @return void
     */
    public function tearDown()
    {
        static::clearCachePool();
    }

    /**
     * Test middleware with the default settings.
     *
     * @covers ::__construct
     * @covers ::defaults
     */
    public function testDefaults()
    {
        $middleware = $this->middlewareFactory();
        $defaults   = $middleware->defaults();

        $this->assertAttributeInstanceOf(Pool::class, 'cachePool', $middleware);

        $this->assertArrayHasKey('ttl', $defaults);
        $this->assertAttributeEquals($defaults['ttl'], 'cacheTtl', $middleware);

        $this->assertArrayHasKey('included_path', $defaults);
        $this->assertAttributeEquals($defaults['included_path'], 'includedPath', $middleware);

        $this->assertArrayHasKey('excluded_path', $defaults);
        $this->assertAttributeEquals($defaults['excluded_path'], 'excludedPath', $middleware);

        $this->assertArrayHasKey('methods', $defaults);
        $this->assertAttributeEquals($defaults['methods'], 'methods', $middleware);

        $this->assertArrayHasKey('status_codes', $defaults);
        $this->assertAttributeEquals($defaults['status_codes'], 'statusCodes', $middleware);

        $this->assertArrayHasKey('included_query', $defaults);
        $this->assertAttributeEquals($defaults['included_query'], 'includedQuery', $middleware);

        $this->assertArrayHasKey('excluded_query', $defaults);
        $this->assertAttributeEquals($defaults['excluded_query'], 'excludedQuery', $middleware);

        $this->assertArrayHasKey('ignored_query', $defaults);
        $this->assertAttributeEquals($defaults['ignored_query'], 'ignoredQuery', $middleware);
    }

    /**
     * Test middleware with an invalid HTTP request method.
     *
     * @covers ::__invoke
     * @covers ::isRequestMethodValid
     * @covers ::isResponseStatusValid
     * @covers ::isPathIncluded
     * @covers ::isPathExcluded
     * @covers ::isQueryIncluded
     * @covers ::isQueryExcluded
     * @covers ::parseIgnoredParams
     *
     * @dataProvider provideInvokableSituations
     *
     * @param  boolean $expected    The expected result from {@see \Psr\Cache\CacheItemInterface::isHit()}.
     * @param  stromg  $requestUri  The request URI for {@see self::createRequest()}.
     * @param  array   $cacheConfig The CacheMiddleware settings.
     * @return void
     */
    public function testInvoke($expected, $requestUri, array $cacheConfig)
    {
        $middleware = $this->middlewareFactory($cacheConfig);
        $request    = $this->createRequest('GET', $requestUri);
        $response   = $this->createResponse();
        $finalize   = $this->mockFinalMiddleware('Hello, World!', 200);

        $result = $middleware($request, $response, $finalize);

        // Validate the HTTP response
        $this->assertEquals('Hello, World!', (string) $result->getBody());
        $this->assertEquals(200, $result->getStatusCode());

        // Validate that the HTTP response is NOT cached
        $pool = $this->getCachePool();
        $item = $pool->getItem('request/GET/' . md5((string) $request->getUri()));

        $this->assertEquals($expected, $item->isHit());
    }

    /**
     * Provide data for testing the middleware.
     *
     * @used-by self::testInvoke()
     * @return  array
     */
    public function provideInvokableSituations()
    {
        $target1 = '/foo/bar';
        $target2 = '/foo/bar?abc=123';
        $target3 = '/foo/bar?abc=123&def=456';
        $target4 = '/foo/bar?=';

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'methods → accept one'          => [ false, $target1, [ 'methods'        => 'HEAD' ] ],

            'status_codes → accept one'     => [ false, $target1, [ 'status_codes'   => 201 ] ],

            'included_path → accept all'    => [ true,  $target1, [ 'included_path'  => '*' ] ],
            'included_path → accept #1'     => [ true,  $target1, [ 'included_path'  => '^/(foo|qux)/bar' ] ],
            'included_path → accept #2'     => [ false, $target1, [ 'included_path'  => '^/[xyz]+/bar' ] ],
            'included_path → empty'         => [ false, $target1, [ 'included_path'  => [] ] ],

            'excluded_path → reject all'    => [ false, $target1, [ 'excluded_path'  => '*' ] ],
            'excluded_path → reject #1'     => [ false, $target1, [ 'excluded_path'  => '^/(foo|qux)/bar' ] ],
            'excluded_path → reject #2'     => [ true,  $target1, [ 'excluded_path'  => '^/[xyz]+/bar' ] ],
            'excluded_path → empty'         => [ true,  $target1, [ 'excluded_path'  => [] ] ],

            'included_query → accept all'   => [ true,  $target2, [ 'included_query' => '*' ] ],
            'included_query → accept #1'    => [ true,  $target2, [ 'included_query' => 'abc' ] ],
            'included_query → accept #2'    => [ false, $target2, [ 'included_query' => 'def' ] ],
            'included_query → empty'        => [ false, $target2, [ 'included_query' => [] ] ],

            'ignored_query → ignore all #1' => [ false, $target3, [ 'ignored_query'  => '*' ] ],
            'ignored_query → ignore all #2' => [ true,  $target3, [ 'ignored_query'  => '*', 'included_query'   => '*' ] ],
            'ignored_query → ignore all #3' => [ false, $target3, [ 'ignored_query'  => '*', 'included_query'   => 'abc' ] ],
            'ignored_query → ignore #1'     => [ false, $target3, [ 'ignored_query'  => [ 'abc', 'def' ] ] ],
            'ignored_query → ignore #2'     => [ false, $target3, [ 'ignored_query'  => 'def' ] ],
            'ignored_query → bad query'     => [ false, $target4, [ 'ignored_query'  => [] ] ],
            'ignored_query → empty'         => [ false, $target3, [ 'ignored_query'  => [] ] ],

            'excluded_query → reject all'   => [ false, $target3, [ 'excluded_query' => '*',   'included_query' => '*' ] ],
            'excluded_query → reject #1'    => [ false, $target3, [ 'excluded_query' => 'abc', 'included_query' => '*' ] ],
            'excluded_query → reject #2'    => [ false, $target3, [ 'excluded_query' => 'def', 'included_query' => '*' ] ],
            'excluded_query → empty'        => [ true,  $target3, [ 'excluded_query' => [],    'included_query' => '*' ] ],
        ];
        // phpcs:enable
    }
}
