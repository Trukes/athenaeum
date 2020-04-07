<?php

namespace Aedart\Tests\Integration\Http\Clients\Query;

use Aedart\Contracts\Http\Clients\Exceptions\HttpQueryBuilderException;
use Aedart\Contracts\Http\Clients\Exceptions\ProfileNotFoundException;
use Aedart\Testing\Helpers\ConsoleDebugger;
use Aedart\Tests\TestCases\Http\HttpClientsTestCase;

/**
 * C5_WhereObjectTest
 *
 * @group http-clients
 * @group http-query
 * @group http-query-c6
 * @group http-query-grammars
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Http\Clients\Query
 */
class C6_WhereObjectTest extends HttpClientsTestCase
{
    /*****************************************************************
     * Data Providers
     ****************************************************************/

    /**
     * Provides data for where object test
     *
     * @return array
     */
    public function providesWhereObject()
    {
        return [
            'default' => [
                'default',
                'address=Somewhere Str. 41'
            ],
            'json api' => [
                'json_api',
                'filter[address]=Somewhere Str. 41'
            ],

            // Note: Here the syntax is not right, yet this matters not for
            // this test, where we just want to see that objects are cast
            // into strings.
            'odata' => [
                'odata',
                '$filter=address eq Somewhere Str. 41'
            ],
        ];
    }

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     * @dataProvider providesWhereObject
     *
     * @param string $grammar
     * @param string $expected
     *
     * @throws ProfileNotFoundException
     * @throws HttpQueryBuilderException
     */
    public function canAddWhereObject(string $grammar, string $expected)
    {
        $address = new class() {
            public function __toString()
            {
                return 'Somewhere Str. 41';
            }
        };

        $result = $this
            ->query($grammar)
            ->where('address', $address)
            ->build();

        ConsoleDebugger::output($result);

        $this->assertSame($expected, $result);
    }
}
