<?php

namespace Aedart\Tests\Unit\ETags;

use Aedart\Contracts\ETags\ETag as ETagInterface;
use Aedart\Contracts\ETags\Exceptions\ETagException;
use Aedart\ETags\ETag;
use Aedart\ETags\Exceptions\InvalidRawValue;
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
    public function failsWhenEmptyRawValue(): void
    {
        $this->expectException(InvalidRawValue::class);

        ETag::make('');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canParseSingleValue(): void
    {
        $raw = '0815';
        $value = 'W/"' . $raw . '"';

        $etag = ETag::parseSingle($value);

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
    public function canParseWildcard(): void
    {
        $etag = ETag::parseSingle('*');

        ConsoleDebugger::output($etag);

        $this->assertTrue($etag->isWildcard());
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function failsParsingSingleWhenMultipleEtagsGiven(): void
    {
        $this->expectException(UnableToParseETag::class);

        ETag::parseSingle('"1324", "abcd"');
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

        ETag::parseSingle($value);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canParseMultipleEtagsFromHttpHeader(): void
    {
        $etags = ETag::parse('"15487",W/"r2d23574", W/"c3pio784",  W/"1234", *');

        ConsoleDebugger::output($etags);

        $this->assertIsArray($etags);
        $this->assertCount(5, $etags);

        foreach ($etags as $etag) {
            $this->assertInstanceOf(ETagInterface::class, $etag);
        }

        $this->assertSame('15487', $etags[0]->raw(), 'a');
        $this->assertTrue($etags[0]->isStrong(), 'b');

        $this->assertSame('r2d23574', $etags[1]->raw(), 'c');
        $this->assertTrue($etags[1]->isWeak(), 'd');

        $this->assertSame('c3pio784', $etags[2]->raw(), 'e');
        $this->assertFalse($etags[2]->isStrong(), 'f');

        $this->assertSame('1234', $etags[3]->raw(), 'g');
        $this->assertFalse( $etags[3]->isWildcard(), 'h');

        $this->assertSame('*', $etags[4]->raw(), 'i');
        $this->assertTrue($etags[4]->isWildcard(), 'j');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function failsParsingMultipleFromHttpHeaderWhenInvalidFormat(): void
    {
        $this->expectException(UnableToParseETag::class);

        ETag::parse('"15487",W/"r2d23574", "invalid,  W/"1234",');
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

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canMatchEtags(): void
    {
        //  @see https://httpwg.org/specs/rfc9110.html#rfc.section.8.8.3.2
        /**
            ETag 1 	ETag 2 	Strong Comparison 	Weak Comparison
            W/"1" 	W/"1" 	no match 	        match
            W/"1" 	W/"2" 	no match 	        no match
            W/"1" 	"1" 	no match 	        match
            "1" 	"1" 	match 	            match
         */

        // ----------------------------------------------------------- //
        // ETag 1 	ETag 2 	Strong Comparison 	Weak Comparison
        // W/"1" 	W/"1" 	no match 	        match
        $etagA = ETag::parseSingle('W/"0815"');
        $etagB = ETag::parseSingle('W/"0815"');

        $this->assertFalse($etagA->matches($etagB, true), '(a) strong comparison should NOT match');
        $this->assertTrue($etagA->matches($etagB), '(b) weak comparison should match');

        // ----------------------------------------------------------- //
        // ETag 1 	ETag 2 	Strong Comparison 	Weak Comparison
        // W/"1" 	W/"2" 	no match 	        no match
        $etagA = ETag::parseSingle('W/"0815"');
        $etagB = ETag::parseSingle('W/"0932"');

        $this->assertFalse($etagA->matches($etagB, true), '(c) strong comparison should NOT match');
        $this->assertFalse($etagA->matches($etagB), '(d) weak comparison should NOT match');

        // ----------------------------------------------------------- //
        // ETag 1 	ETag 2 	Strong Comparison 	Weak Comparison
        // W/"1" 	"1" 	no match 	        match
        $etagA = ETag::parseSingle('W/"0815"');
        $etagB = ETag::parseSingle('"0815"');

        $this->assertFalse($etagA->matches($etagB, true), '(e) strong comparison should NOT match');
        $this->assertTrue($etagA->matches($etagB), '(f) weak comparison should match');

        // ----------------------------------------------------------- //
        // ETag 1 	ETag 2 	Strong Comparison 	Weak Comparison
        // "1" 	    "1" 	match 	            match
        $etagA = ETag::parseSingle('"0815"');
        $etagB = ETag::parseSingle('"0815"');

        $this->assertTrue($etagA->matches($etagB, true), '(g) strong comparison should match');
        $this->assertTrue($etagA->matches($etagB), '(h) weak comparison should match');

        // ----------------------------------------------------------- //
        // Not match test...
        $etagA = ETag::parseSingle('W/"0815"');
        $etagB = ETag::parseSingle('"0815"');

        $this->assertTrue($etagA->doesNotMatch($etagB, true), '(i) strong comparison should NOT match');
        $this->assertFalse($etagA->doesNotMatch($etagB), '(j) weak comparison should match');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canMatchAgainstWildcard(): void
    {
        $etagA = ETag::parseSingle('*');
        $etagB = ETag::parseSingle('W/"0815"');

        $this->assertTrue($etagA->matches($etagB, true), '(a) should match wildcard - strong comparison');
        $this->assertFalse($etagA->doesNotMatch($etagB), '(b) weak comparison should match');

        $this->assertTrue($etagB->matches($etagA, true), '(c) should match wildcard - strong comparison');
        $this->assertFalse($etagB->doesNotMatch($etagA), '(d) weak comparison should match');
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws ETagException
     */
    public function canMatchAgainstSingleValueFromHttpHeader(): void
    {
        $etag = ETag::make(1234);

        $this->assertTrue($etag->matches('"1234"'), '(a) should had matched value');
        $this->assertFalse($etag->doesNotMatch('"1234"'), '(b) should had matched value');
    }
}