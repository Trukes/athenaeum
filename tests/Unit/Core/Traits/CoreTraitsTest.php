<?php

namespace Aedart\Tests\Unit\Core\Traits;

use Aedart\Core\Traits\NamespaceDetectorTrait;
use Aedart\Core\Traits\PathsContainerTrait;
use Aedart\Tests\TestCases\TraitTestCase;

/**
 * CoreTraitsTest
 *
 * @group core
 * @group application
 * @group application-traits
 * @group traits
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Unit\Core\Traits
 */
class CoreTraitsTest extends TraitTestCase
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
            'PathsContainerTrait'           => [ PathsContainerTrait::class ],
            'NamespaceDetectorTrait'        => [ NamespaceDetectorTrait::class ],
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
