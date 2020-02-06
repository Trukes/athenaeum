<?php

namespace Aedart\Tests\Integration\Config;

use Aedart\Config\Providers\ConfigLoaderServiceProvider;
use Aedart\Config\Traits\ConfigLoaderTrait;
use Aedart\Support\Helpers\Config\ConfigTrait;
use Aedart\Testing\Helpers\ConsoleDebugger;
use Aedart\Testing\TestCases\LaravelTestCase;
use Codeception\Configuration;
use Illuminate\Config\Repository;

/**
 * ConfigLoaderTest
 *
 * @group config
 * @group config-loader
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Config
 */
class ConfigLoaderTest extends LaravelTestCase
{
    use ConfigLoaderTrait;
    use ConfigTrait;

    /*****************************************************************
     * Setup Methods
     ****************************************************************/

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            ConfigLoaderServiceProvider::class
        ];
    }

    /*****************************************************************
     * Helpers
     ****************************************************************/

    /**
     * Returns the path to configuration files
     *
     * @return string
     */
    public function directory() : string
    {
        return Configuration::dataDir() . 'configs/loader/';
    }

    /*****************************************************************
     * Actual Tests
     ****************************************************************/

    /**
     * @test
     */
    public function canObtainInstance()
    {
        $loader = $this->getConfigLoader();

        $this->assertNotNull($loader);
    }

    /**
     * @test
     *
     * @throws \Aedart\Contracts\Config\Loaders\Exceptions\InvalidPathException
     */
    public function convertsRelativePathsToRealPaths()
    {
        $loader = $this->getConfigLoader();

        $path = __DIR__ . '/../../_data/configs/loader';
        $loader->setDirectory($path);

        $convertedPath = $loader->getDirectory();

        $this->assertSame($this->directory(), $convertedPath);
    }

    /**
     * @test
     */
    public function canLoadConfigurationFiles()
    {
        $loader = $this->getConfigLoader();
        $loader
            ->setConfig(new Repository([]))
            ->setDirectory($this->directory())
            ->load();

        $config = $loader->getConfig();

        ConsoleDebugger::output($config->all());

        // -------------------------------------------- //

        // Assert various parsers
        $this->assertSame('Hallo World', $config->get('array.message'));
        $this->assertSame('http://www.example.com/~username', $config->get('ini.second_section.URL'));
        $this->assertSame('mail.test.com', $config->get('json.email.server'));
        $this->assertSame('tests/_envs', $config->get('yaml.paths.envs'));

        // Assert nested level
        $this->assertSame('Nested message', $config->get('nested.array.message'));
        $this->assertSame(true, $config->get('nested.ini.first_section.my_state'));
        $this->assertSame('Rick Johnson', $config->get('nested.json.name'));
        $this->assertSame('James Brown Jr.', $config->get('nested.yaml.actor'));

        // Assert deep nested ... (just to be sure!)
        $this->assertSame('Deep nested message', $config->get('nested.deep.module.message'));
    }
}
