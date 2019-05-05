<?php

namespace Aedart\Tests\Unit\Http\Clients\Traits;

use Aedart\Http\Clients\Traits\HttpClientsManagerTrait;
use Aedart\Tests\TestCases\TraitTestCase;

/**
 * Http Clients Traits Test
 *
 * @group http
 * @group http-clients
 * @group traits
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Unit\Http\Clients\Traits
 */
class HttpClientsTraitsTest extends TraitTestCase
{
    /*****************************************************************
     * Providers
     ****************************************************************/

    /**
     * @return array
     */
    public function awareOfComponentsProvider()
    {
        return [
            'HttpClientsManagerTrait'        => [ HttpClientsManagerTrait::class ],
        ];
    }

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     * @dataProvider awareOfComponentsProvider
     *
     * @param string $awareOfTrait
     *
     * @throws \ReflectionException
     */
    public function canInvokeAwareOfMethods(string $awareOfTrait)
    {
        $this->assertTraitMethods($awareOfTrait, null, null);
    }
}