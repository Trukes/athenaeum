<?php

namespace Aedart\Tests\Unit\ETags;

use Aedart\Contracts\ETags\ETag as ETagInterface;
use Aedart\Contracts\ETags\Exceptions\ETagException;
use Aedart\ETags\ETag;
use Aedart\ETags\Exceptions\UnableToParseETag;
use Aedart\Testing\Helpers\ConsoleDebugger;
use Aedart\Testing\TestCases\UnitTestCase;

/**
 * ETagTest
 *
 * @group etags
 * @group etag-dto
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Unit\ETags
 */
class ETagTest extends UnitTestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function canMakeNewInstance(): void
    {
        $raw = '1234';
        $etag = ETag::make($raw);

        ConsoleDebugger::output($etag);

        // When no failure, then test passes
        $this->assertInstanceOf(ETagInterface::class, $etag);
        $this->assertSame($raw, $etag->raw(), 'Invalid raw value');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canParseFromHttpHeaderValue(): void
    {
        $raw = '0815';
        $value = 'W/"' . $raw . '"';

        $etag = ETag::parse($value);

        ConsoleDebugger::output($etag);

        $this->assertSame($raw, $etag->raw());
        $this->assertTrue($etag->isWeak());
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function failsParsingWhenValueInvalid(): void
    {
        $this->expectException(UnableToParseETag::class);

        $raw = '0815';
        $value = '/"' . $raw;

        ETag::parse($value);
    }

    /**
     * @test
     *
     * @return void
     */
    public function formatsValueCorrectly(): void
    {
        $etag = ETag::make(1234);
        $weakETag = ETag::make(1234, true);

        $this->assertSame('"1234"', (string) $etag);
        $this->assertSame('W/"1234"', (string) $weakETag);
    }

    public function canMatchAgainstAnotherEtag()
    {
        $etagA = ETag::make(1234);
        $etagB = ETag::make(1234);
        $etagC = ETag::make(4321);


    }
}