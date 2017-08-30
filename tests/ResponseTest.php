<?php

namespace Laravie\Codex\TestCase;

use Mockery as m;
use Laravie\Codex\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    function it_can_build_a_basic_response()
    {
        $json = '{"name":"Laravie Codex"}';
        $data = ['name' => 'Laravie Codex'];

        $api = m::mock(ResponseInterface::class);

        $api->shouldReceive('getBody')->twice()->andReturn($json);

        $stub = new Response($api);

        $this->assertSame($data, $stub->toArray());
        $this->assertSame($json, $stub->getBody());
    }

    /** @test */
    function it_can_return_status_code()
    {
        $api = m::mock(ResponseInterface::class);

        $api->shouldReceive('getStatusCode')->once()->andReturn(201);

        $stub = new Response($api);

        $this->assertSame(201, $stub->getStatusCode());
    }
}
