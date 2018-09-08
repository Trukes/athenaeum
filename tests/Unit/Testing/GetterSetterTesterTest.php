<?php

namespace Aedart\Tests\Unit\Testing;

use Aedart\Tests\Helpers\Dummies\Traits\NameTrait;
use Aedart\Tests\TestCases\TraitTestCase;

/**
 * TraitTesterTest
 *
 * @group testing
 * @group trait-tester
 * @group getter-setter-tester
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Unit\Testing
 */
class GetterSetterTesterTest extends TraitTestCase
{
    /**
     * @test
     */
    public function canAssertTraitMethods()
    {
        $this->assertGetterSetterTraitMethods(
            NameTrait::class,
            $this->faker->name,
            $this->faker->name
        );
    }
}
