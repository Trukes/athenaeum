<?php

namespace Aedart\Tests\Integration\Validation\Rules;

use Aedart\Tests\TestCases\Validation\ValidationTestCase;
use Aedart\Validation\Rules\SemanticVersion;
use Illuminate\Contracts\Validation\Rule;

/**
 * SemanticVersionRuleTest
 *
 * @group validation
 * @group rules
 * @group version
 *
 * Some of the valid and invalid data sets are found from the following regex example:
 * https://regex101.com/r/Ly7O1x/3/
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Validation\Rules
 */
class SemanticVersionRuleTest extends ValidationTestCase
{
    /*****************************************************************
     * Data Providers
     ****************************************************************/

    /**
     * Provides input that should pass validation
     *
     * @return \string[][]
     */
    public function validInput(): array
    {
        return [
            'major.minor.patch' => [ '6.3.14' ],
            'major.minor.patch-[pre-release]' => [ '2.0.0-alpha' ],
            'major.minor.patch-[pre-release]+[build-metadata]' => [ '1.0.0-stable+exp.sha.5114f85' ],
        ];
    }

    /**
     * Provides input that should not pass validation
     *
     * @return \string[][]
     */
    public function invalidInput(): array
    {
        return [
            'major only' => [ '1' ],
            'major.minor only' => [ '3.1' ],
            'invalid pre-release' => [ '1.2.3-0123' ],
            'invalid build metadata' => [ '1.1.2+.123' ],
            'invalid format' => [ 'invalid' ],
            'negative version' => [ '-1.0.3-gamma+b7718' ],
            'invalid prefix' => [ 'v1.0.3-gamma+b7718' ],
        ];
    }

    /*****************************************************************
     * Helpers
     ****************************************************************/

    /**
     * Creates new instance of validation rule
     *
     * @return Rule
     */
    public function makeRule(): Rule
    {
        return new SemanticVersion();
    }

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     * @dataProvider validInput
     *
     * @param mixed $input
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function passesOnValidInput($input)
    {
        $this->shouldPass($input, $this->makeRule());
    }

    /**
     * @test
     * @dataProvider invalidInput
     *
     * @param mixed $input
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failsOnInvalidInput($input)
    {
        $this->shouldNotPass($input, $this->makeRule());
    }
}
