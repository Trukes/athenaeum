<?php


namespace Aedart\Testing\TestCases;

use Aedart\Contracts\Core\Application;
use Aedart\Contracts\Core\Helpers\NamespaceDetectorAware;
use Aedart\Contracts\Core\Helpers\PathsContainerAware;
use Aedart\Contracts\Service\ServiceProviderRegistrarAware;
use Aedart\Contracts\Support\Helpers\Config\ConfigAware;
use Aedart\Contracts\Support\Helpers\Events\EventAware;
use Aedart\Core\Application as CoreApplication;
use Codeception\Configuration;

/**
 * Application Integration Test Case
 *
 * Base test-case for integration tests, using an application
 *
 * @see \Aedart\Contracts\Core\Application
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Testing\TestCases
 */
abstract class ApplicationIntegrationTestCase extends IntegrationTestCase
{
    /**
     * Application instance
     *
     * @var Application|PathsContainerAware|ServiceProviderRegistrarAware|ConfigAware|EventAware|NamespaceDetectorAware|null
     */
    protected $app = null;

    /**
     * State of application's exception handling.
     *
     * @var bool
     */
    protected bool $forceThrowExceptions = true;

    /*****************************************************************
     * Setup
     ****************************************************************/

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        parent::_before();

        // (Re)register container, use application
        // instead.
        $this->ioc->destroy();

        $this->app = $this->createApplication();
        $this->ioc = $this->app;
    }

    /**
     * @inheritdoc
     */
    protected function _after()
    {
        // Destroy application before destroying ioc
        if(isset($this->app)){
            $this->app->destroy();
            $this->app = null;
        }

        parent::_after();
    }

    /**
     * Creates a new application instance
     *
     * @return Application
     *
     * @throws \Throwable
     */
    protected function createApplication() : Application
    {
        // Create application
        $app = new CoreApplication(
            $this->applicationPaths(),
            'x.x.x-testing'
        );

        // Detect "testing" environment
        $app->detectEnvironment(fn() => $this->detectEnvironment());

        // Final setup and return the instance
        return $app
            ->forceThrowExceptions($this->forceThrowExceptions);
    }

    /**
     * Returns the paths that the application must use
     *
     * @return array
     *
     * @throws \Codeception\Exception\ConfigurationException
     */
    protected function applicationPaths() : array
    {
        return [
            'basePath'          => getcwd(),
            'bootstrapPath'     => Configuration::dataDir() . 'bootstrap',
            'configPath'        => Configuration::dataDir() . 'config',
            'databasePath'      => Configuration::outputDir() . 'database',
            'environmentPath'   => getcwd(),
            'resourcePath'      => Configuration::dataDir() . 'resources',
            'storagePath'       => Configuration::dataDir()
        ];
    }

    /**
     * Detects the environment the application must use
     *
     * @return string
     */
    protected function detectEnvironment() : string
    {
        return 'testing';
    }

    /*****************************************************************
     * Helpers
     ****************************************************************/

}
