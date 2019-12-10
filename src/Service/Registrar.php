<?php


namespace Aedart\Service;

use Aedart\Contracts\Service\Registrar as RegistrarInterface;
use Aedart\Support\Helpers\Container\ContainerTrait;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider Registrar
 *
 * @see \Aedart\Contracts\Service\Registrar
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Service
 */
class Registrar implements RegistrarInterface
{
    use ContainerTrait;

    /**
     * List of registered service providers
     *
     * @var ServiceProvider[]
     */
    protected array $providers = [];

    /**
     * List of booted service providers
     *
     * @var ServiceProvider[]
     */
    protected array $bootedServiceProviders = [];

    /**
     * Registrar constructor.
     *
     * @param Container|null $container [optional] IoC Service Container
     */
    public function __construct(?Container $container = null)
    {
        $this->setContainer($container);
    }

    /**
     * @inheritDoc
     */
    public function register($provider, bool $boot = true) : bool
    {
        // Abort if already registered and not forced to register
        if($this->isRegistered($provider)){
            return false;
        }

        // Create provider instance or return given
        $provider = $this->resolveProviderInstance($provider);

        // Register provider and it's internal bindings & singletons
        $this->performRegistration($provider);

        // Boot provider if required
        if($boot){
            $this->boot($provider);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function registerMultiple(array $providers, bool $boot = true, bool $safeBoot = true) : void
    {
        // If invoked with "safe boot false", then we must boot
        // each provider immediately, during it's registration
        $shouldBootImmediately = $boot && ! $safeBoot;

        foreach ($providers as $provider){
            $this->register($provider, $shouldBootImmediately);
        }

        if($boot && $safeBoot){
            // We have to boot instances registered, not given
            // list of providers, which might just be class paths!
            $this->bootMultiple($this->providers());
        }
    }

    /**
     * @inheritdoc
     */
    public function boot(ServiceProvider $provider) : bool
    {
        if($this->hasBooted($provider)){
            return false;
        }

        // Boot provider in the same way that Laravel's Application
        // does such.
        // @see https://github.com/laravel/framework/blob/6.x/src/Illuminate/Foundation/Application.php#L826
        if(method_exists($provider, 'boot')){
            $this->getContainer()->call([$provider, 'boot']);

            $this->bootedServiceProviders[] = $provider;

            return true;
        }

        // Means that provider didn't contain a boot method
        return false;
    }

    /**
     * @inheritdoc
     */
    public function bootMultiple(array $providers) : void
    {
        array_walk($providers, function(ServiceProvider $provider){
            $this->boot($provider);
        });
    }

    /**
     * @inheritdoc
     */
    public function bootAll() : void
    {
        $this->bootMultiple($this->providers());
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($provider) : bool
    {
        $providers = $this->providers();
        if($provider instanceof ServiceProvider){
            return in_array($provider, $providers);
        }

        // In case that a string has been given,...
        $name = $this->providerNamespace($provider);
        foreach ($providers as $registered){
            if($registered instanceof $name){
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasBooted(ServiceProvider $provider) : bool
    {
        return in_array($provider, $this->booted());
    }

    /**
     * @inheritdoc
     */
    public function providers() : array
    {
        return $this->providers;
    }

    /**
     * @inheritdoc
     */
    public function booted() : array
    {
        return $this->bootedServiceProviders;
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Performs the registration of given service provider
     *
     * @param ServiceProvider $provider
     */
    protected function performRegistration(ServiceProvider $provider)
    {
        $provider->register();

        // Resolve "bindings" and "singletons" properties.
        // This is done in the exact same way as in Laravel's Application.
        // @see https://github.com/laravel/framework/blob/6.x/src/Illuminate/Foundation/Application.php#L596
        $ioc = $this->getContainer();

        // Bindings
        if(property_exists($provider, 'bindings')){
            foreach ($provider->bindings as $abstract => $concrete){
                $ioc->bind($abstract, $concrete);
            }
        }

        // Singletons
        if(property_exists($provider, 'singletons')){
            foreach ($provider->singletons as $abstract => $concrete){
                $ioc->singleton($abstract, $concrete);
            }
        }

        // Add provider to list of registered providers
        $this->providers[] = $provider;
    }

    /**
     * Returns the given service provider's namespace
     *
     * @param ServiceProvider|string $provider
     *
     * @return string
     */
    protected function providerNamespace($provider) : string
    {
        return is_string($provider) ? $provider : get_class($provider);
    }

    /**
     * Create service provider instance, if required
     *
     * @param ServiceProvider|string $provider
     *
     * @return ServiceProvider
     */
    protected function resolveProviderInstance($provider) : ServiceProvider
    {
        if($provider instanceof ServiceProvider){
            return $provider;
        }

        return new $provider($this->getContainer());
    }
}
