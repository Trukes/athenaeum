<?php

namespace Aedart\Tests\Unit\ETags\Traits;

use Aedart\ETags\Traits\ETagGeneratorFactoryTrait;
use Aedart\Tests\TestCases\TraitTestCase;

/**
 * ETagsTraitsTest
 *
 * @group etags
 * @group etags-traits
 * @group traits
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Unit\ETags\Traits
 */
class ETagsTraitsTest extends TraitTestCase
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
            'ETagGeneratorFactoryTrait' => [ ETagGeneratorFactoryTrait::class ],
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
