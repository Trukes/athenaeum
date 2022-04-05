<?php

namespace Aedart\Tests\Integration\Streams;

use Aedart\Streams\Exceptions\InvalidStreamResource;
use Aedart\Streams\Stream;
use Aedart\Tests\TestCases\Streams\StreamTestCase;

/**
 * A0_InstanceTest
 *
 * @group streams
 * @group streams-a0
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Streams
 */
class A0_InstanceTest extends StreamTestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function canCreateNewInstance()
    {
        $stream = new Stream();

        // Well... if no exception is thrown, then test passes...
        $this->assertTrue($stream->isDetached());
        $this->assertFalse($stream->isOpen());
    }

    /**
     * @test
     *
     * @return void
     */
    public function canCreateInstanceWithResource()
    {
        $resource = fopen('php://memory', 'r+b');

        $stream = new Stream($resource);

        $this->assertFalse($stream->isDetached());
        $this->assertTrue($stream->isOpen());
    }

    /**
     * @test
     *
     * @return void
     */
    public function failsWhenResourceIsInvalid()
    {
        $this->expectException(InvalidStreamResource::class);

        new Stream('invalid-resource');
    }

    /**
     * @test
     *
     * @return void
     */
    public function canCreateInstanceWithMeta()
    {
        $stream = new Stream(
            meta: [ 'foo' => 'bar' ]
        );

        $result = $stream->getMetadata('foo');

        $this->assertSame('bar', $result);
    }
}
