<?php

namespace Aedart\Tests\Integration\Http\Api\Registrar;

use Aedart\Tests\Helpers\Dummies\Http\Api\Models\Game;
use Aedart\Tests\Helpers\Dummies\Http\Api\Models\Owner;
use Aedart\Tests\Helpers\Dummies\Http\Api\Resources\GameResource;
use Aedart\Tests\Helpers\Dummies\Http\Api\Resources\OwnerResource;
use Aedart\Tests\TestCases\Http\ApiResourcesTestCase;

/**
 * RegistrarTest
 *
 * @group http-api
 * @group api-resource-registrar
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Http\Api\Registrar
 */
class RegistrarTest extends ApiResourcesTestCase
{
    /**
     * When true, migrations for this test-case will
     * be installed.
     *
     * @var bool
     */
    protected bool $installMigrations = false;

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     *
     * @return void
     */
    public function canRegisterFromConfiguration(): void
    {
        $registrar = $this->getApiResourceRegistrar();
        $registrar->register(
            $this->getConfig()->get('api-resources.registry', [])
        );

        $result = $registrar->has(Game::class);

        $this->assertTrue($result);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canSetResourceUsingInstances(): void
    {
        $registrar = $this->getApiResourceRegistrar();

        $registrar->set(new Game(), GameResource::make(null));

        // ------------------------------------------------------------ //

        $result = $registrar->has(new Game());

        $this->assertTrue($result);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canGetResourceClassForModel(): void
    {
        $registrar = $this->getApiResourceRegistrar();
        $registrar->register([
            Game::class => GameResource::class,
            Owner::class => OwnerResource::class
        ]);

        // ------------------------------------------------------------ //

        $result = $registrar->get(Owner::class);

        $this->assertSame(OwnerResource::class, $result);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canFindModelByResourceType(): void
    {
        $registrar = $this->getApiResourceRegistrar();
        $registrar->register([
            Game::class => GameResource::class,
            Owner::class => OwnerResource::class
        ]);

        // ------------------------------------------------------------ //

        $result = $registrar->findModelByType('game');

        $this->assertSame(Game::class, $result);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canFindResourceByType(): void
    {
        $registrar = $this->getApiResourceRegistrar();
        $registrar->register([
            Game::class => GameResource::class,
            Owner::class => OwnerResource::class
        ]);

        // ------------------------------------------------------------ //

        $result = $registrar->findResourceByType('owners');

        $this->assertSame(OwnerResource::class, $result);
    }

    /**
     * @test
     *
     * @return void
     */
    public function canForgetApiResource(): void
    {
        $registrar = $this->getApiResourceRegistrar();
        $registrar->register([
            Game::class => GameResource::class,
            Owner::class => OwnerResource::class
        ]);

        // ------------------------------------------------------------ //

        $result = $registrar->forget(Owner::class);

        $this->assertTrue($result, 'Failed to forget Api Resource');
        $this->assertFalse($registrar->has(Owner::class), 'Api resource still registered for model!');
        $this->assertNull($registrar->findModelByType('owner'), 'A model was found for Api resource type, but should not be');
    }
}