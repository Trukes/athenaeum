<?php

namespace Aedart\Tests\Integration\Support\Helpers;

use Aedart\Support\Helpers\Auth\AuthFactoryTrait;
use Aedart\Support\Helpers\Auth\AuthTrait;
use Aedart\Support\Helpers\Auth\PasswordBrokerFactoryTrait;
use Aedart\Support\Helpers\Auth\PasswordTrait;
use Aedart\Support\Helpers\Broadcasting\BroadcastFactoryTrait;
use Aedart\Support\Helpers\Broadcasting\BroadcastTrait;
use Aedart\Support\Helpers\Bus\BusTrait;
use Aedart\Support\Helpers\Bus\QueueingBusTrait;
use Aedart\Support\Helpers\Cache\CacheFactoryTrait;
use Aedart\Support\Helpers\Cache\CacheStoreTrait;
use Aedart\Support\Helpers\Cache\CacheTrait;
use Aedart\Support\Helpers\Config\ConfigTrait;
use Aedart\Support\Helpers\Console\ArtisanTrait;
use Aedart\Support\Helpers\Container\ContainerTrait;
use Aedart\Support\Helpers\Cookie\CookieTrait;
use Aedart\Support\Helpers\Cookie\QueueingCookieTrait;
use Aedart\Support\Helpers\Database\ConnectionResolverTrait;
use Aedart\Support\Helpers\Database\DbTrait;
use Aedart\Support\Helpers\Database\SchemaTrait;
use Aedart\Support\Helpers\Encryption\CryptTrait;
use Aedart\Support\Helpers\Events\DispatcherTrait;
use Aedart\Support\Helpers\Events\EventTrait;
use Aedart\Support\Helpers\Filesystem\CloudStorageTrait;
use Aedart\Support\Helpers\Filesystem\FileTrait;
use Aedart\Support\Helpers\Filesystem\StorageFactoryTrait;
use Aedart\Support\Helpers\Filesystem\StorageTrait;
use Aedart\Support\Helpers\Foundation\AppTrait;
use Aedart\Support\Helpers\Hashing\HashTrait;
use Aedart\Support\Helpers\Http\ClientFactoryTrait;
use Aedart\Support\Helpers\Http\RequestTrait;
use Aedart\Support\Helpers\Logging\LogManagerTrait;
use Aedart\Support\Helpers\Logging\LogTrait;
use Aedart\Support\Helpers\Mail\MailerTrait;
use Aedart\Support\Helpers\Mail\MailManagerTrait;
use Aedart\Support\Helpers\Mail\MailQueueTrait;
use Aedart\Support\Helpers\Notifications\NotificationDispatcherTrait;
use Aedart\Support\Helpers\Notifications\NotificationFactoryTrait;
use Aedart\Support\Helpers\Queue\QueueFactoryTrait;
use Aedart\Support\Helpers\Queue\QueueMonitorTrait;
use Aedart\Support\Helpers\Queue\QueueTrait;
use Aedart\Support\Helpers\Redis\RedisFactoryTrait;
use Aedart\Support\Helpers\Redis\RedisTrait;
use Aedart\Support\Helpers\Routing\RedirectTrait;
use Aedart\Support\Helpers\Routing\ResponseFactoryTrait;
use Aedart\Support\Helpers\Routing\RouteRegistrarTrait;
use Aedart\Support\Helpers\Routing\UrlGeneratorTrait;
use Aedart\Support\Helpers\Session\SessionManagerTrait;
use Aedart\Support\Helpers\Session\SessionTrait;
use Aedart\Support\Helpers\Translation\TranslationLoaderTrait;
use Aedart\Support\Helpers\Translation\TranslatorTrait;
use Aedart\Support\Helpers\Validation\ValidatorFactoryTrait;
use Aedart\Support\Helpers\View\BladeTrait;
use Aedart\Support\Helpers\View\ViewFactoryTrait;
use Aedart\Testing\Helpers\ArgumentFaker;
use Aedart\Testing\Helpers\TraitTester;
use Aedart\Tests\TestCases\Support\LaravelHelpersTestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Routing\Redirector;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Compilers\BladeCompiler;
use Mockery as m;
use ReflectionException;

/**
 * LaravelSupportHelpersTest
 *
 * @group laravel
 * @group support
 * @group support-helpers
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\Integration\Support\Helpers
 */
class LaravelSupportHelpersTest extends LaravelHelpersTestCase
{
    /*****************************************************************
     * Setup Methods
     ****************************************************************/

    protected function _before()
    {
        parent::_before();

        // Ensure to use "predis" as the default client
        Config::set('database.redis.client', 'predis');
    }

    protected function _after()
    {
        // Prevent laravel from stopping, as this just increases
        // the time it takes to execute
        // @see cleanup() inside this test
    }

    /*****************************************************************
     * Helpers and Utils
     ****************************************************************/

    /**
     * @return array
     */
    public function awareOfComponentsProvider()
    {
        return [
            // Auth
            // TODO: Still unable to mock Gate
            // TODO: @see https://github.com/mockery/mockery/issues/861
            //'GateTrait'                       => [ GateTrait::class ],
            'AuthFactoryTrait' => [ AuthFactoryTrait::class ],
            'AuthTrait' => [ AuthTrait::class ],
            'PasswordBrokerFactoryTrait' => [ PasswordBrokerFactoryTrait::class ],
            'PasswordTrait' => [ PasswordTrait::class ],

            // Broadcasting
            'BroadcastFactoryTrait' => [ BroadcastFactoryTrait::class ],
            'BroadcastTrait' => [ BroadcastTrait::class ],

            // Bus
            'BusTrait' => [ BusTrait::class ],
            'QueueingBusTrait' => [ QueueingBusTrait::class ],

            // Cache
            'CacheFactoryTrait' => [ CacheFactoryTrait::class ],
            'CacheStoreTrait' => [ CacheStoreTrait::class ],
            'CacheTrait' => [ CacheTrait::class ],

            // Config
            'ConfigTrait' => [ ConfigTrait::class ],

            // Console
            'ArtisanTrait' => [ ArtisanTrait::class ],

            // Container
            'ContainerTrait' => [ ContainerTrait::class ],

            // Cookie
            'CookieTrait' => [ CookieTrait::class ],
            'QueueingCookieTrait' => [ QueueingCookieTrait::class ],

            // Database
            'ConnectionResolverTrait' => [ ConnectionResolverTrait::class ],
            'DbTrait' => [ DbTrait::class ],
            'SchemaTrait' => [ SchemaTrait::class ],

            // Encryption
            'CryptTrait' => [ CryptTrait::class ],

            // Events
            'EventTrait' => [ EventTrait::class ],
            'DispatcherTrait' => [ DispatcherTrait::class ],

            // Filesystem
            'CloudStorageTrait' => [ CloudStorageTrait::class ],
            'FileTrait' => [ FileTrait::class, Filesystem::class ],
            'StorageFactoryTrait' => [ StorageFactoryTrait::class ],
            'StorageTrait' => [ StorageTrait::class ],

            // Foundation
            'AppTrait' => [ AppTrait::class ],

            // Hashing
            'HashTrait' => [ HashTrait::class ],

            // Http
            'ClientFactoryTrait' => [ ClientFactoryTrait::class ],
            'RequestTrait' => [ RequestTrait::class, Request::class ],

            // Logging
            'LogManagerTrait' => [ LogManagerTrait::class, LogManager::class ],
            'LogTrait' => [ LogTrait::class ],

            // Mail
            'MailerTrait' => [ MailerTrait::class ],
            'MailerManagerTrait' => [ MailManagerTrait::class ],
            'MailQueueTrait' => [ MailQueueTrait::class ],

            // Notifications
            'NotificationDispatcherTrait' => [ NotificationDispatcherTrait::class ],
            'NotificationFactoryTrait' => [ NotificationFactoryTrait::class ],

            // Queue
            'QueueFactoryTrait' => [ QueueFactoryTrait::class ],
            'QueueMonitorTrait' => [ QueueMonitorTrait::class ],
            'QueueTrait' => [ QueueTrait::class ],

            // Redis
            'RedisFactoryTrait' => [ RedisFactoryTrait::class ],
            'RedisTrait' => [ RedisTrait::class ],

            // Routing
            'RedirectTrait' => [ RedirectTrait::class, Redirector::class ],
            'ResponseFactoryTrait' => [ ResponseFactoryTrait::class ],
            'RouteRegistrarTrait' => [ RouteRegistrarTrait::class ],
            'UrlGeneratorTrait' => [ UrlGeneratorTrait::class ],

            // Session
            'SessionManagerTrait' => [ SessionManagerTrait::class, SessionManager::class ],
            'SessionTrait' => [ SessionTrait::class ],

            // Translation
            'TranslatorTrait' => [ TranslatorTrait::class ],
            'TranslationLoaderTrait' => [ TranslationLoaderTrait::class ],

            // Validation
            'ValidatorFactory' => [ ValidatorFactoryTrait::class ],

            // View
            'BladeTrait' => [ BladeTrait::class, BladeCompiler::class ],
            'ViewFactoryTrait' => [ ViewFactoryTrait::class ],
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
     * @param string|null $laravelComponent  [optional] Class path to Laravel component that must be mocked.
     *
     * @throws ReflectionException
     */
    public function canInvokeAwareOfMethods(string $awareOfTrait, string|null $laravelComponent = null)
    {
        $mockedValue = null;
        if (isset($laravelComponent)) {
            $mockedValue = ArgumentFaker::makeMockFor($laravelComponent);
        }

        // Assert getter and setter methods
        $this->assertTraitMethods($awareOfTrait, $mockedValue, $mockedValue, false);

        // Assert a default method
        $tester = new TraitTester($this, $awareOfTrait, null);
        $getMethod = $tester->getPropertyMethodName();
        $mock = $tester->getTraitMock();

        $value = $mock->$getMethod();
        $this->assertNotNull($value, 'Default value is not set. Please check your Laravel services');
    }

    /**
     * @test
     *
     * depends canInvokeTraitMethods
     */
    public function cleanup()
    {
        $this->stopApplication();
        m::close();
    }
}
