<?php

namespace Aedart\Tests\Integration\Http\Clients\Query;

use Aedart\Testing\Helpers\ConsoleDebugger;
use Aedart\Tests\TestCases\Http\HttpClientsTestCase;

/**
 * C0_WhereTest
 *
 * @group http-clients
 * @group http-query
 * @group http-query-c0
 * @group http-query-grammars
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Http\Clients\Query
 */
class C0_WhereTest extends HttpClientsTestCase
{
    /*****************************************************************
     * Data Providers
     ****************************************************************/

    /**
     * Provides data for where field equals value test
     *
     * @return array
     */
    public function providesWhereFieldEqualsValue(): array
    {
        return [
            'default' => [
                'default',
                '?name=john'
            ]
        ];
    }

    /**
     * Provides data for where with operator and value test
     *
     * @return array
     */
    public function providesWhereWithOperatorAndValue(): array
    {
        return [
            'default' => [
                'default',
                '?year[gt]=2020'
            ]
        ];
    }

    /**
     * Provides data for multiple conditions on same field test
     *
     * @return array
     */
    public function providesMultipleConditionsOnSameField(): array
    {
        return [
            'default' => [
                'default',
                '?year[gt]=2020&year[lt]=2051'
            ]
        ];
    }

    /**
     * Provides data for multiple conditions via array test
     *
     * @return array
     */
    public function providesMultipleConditionsViaArray(): array
    {
        return [
            'default' => [
                'default',
                '?year[gt]=2021&year[lt]=2031&name=john'
            ]
        ];
    }

    /**
     * Provides data for where with array values test
     *
     * @return array
     */
    public function providesWhereWithArrayValue(): array
    {
        return [
            'default' => [
                'default',
                '?users[0]=1&users[1]=2&users[2]=3&users[3]=4'
            ]
        ];
    }

    /**
     * Provides data for where with operator and array values test
     *
     * @return array
     */
    public function providesWhereWithOperatorAndArrayValue(): array
    {
        return [
            'default' => [
                'default',
                '?users[in][0]=1&users[in][1]=2&users[in][2]=3&users[in][3]=4'
            ]
        ];
    }

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     * @dataProvider providesWhereFieldEqualsValue
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddWhereFieldEqualsValue(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where('name', 'john')
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @dataProvider providesWhereWithOperatorAndValue
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddWhereWithOperatorAndValue(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where('year', 'gt', 2020)
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @dataProvider providesMultipleConditionsOnSameField
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddMultipleConditionsOnSameField(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where('year', 'gt', 2020)
            ->where('year', 'lt', 2051)
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @dataProvider providesMultipleConditionsViaArray
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddMultipleConditionsViaArray(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where([
                'year' => [
                    'gt' => 2021,
                    'lt' => 2031
                ],
                'name' => 'john'
            ])
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @dataProvider providesWhereWithArrayValue
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddWhereWithArrayValue(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where('users', [1, 2, 3, 4])
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @dataProvider providesWhereWithOperatorAndArrayValue
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException
     * @throws \Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException
     */
    public function canAddWhereWithOperatorAndArrayValue(string $grammar, string $expected)
    {
        $result = $this
            ->query($grammar)
            ->where('users', 'in', [1, 2, 3, 4])
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }
}
