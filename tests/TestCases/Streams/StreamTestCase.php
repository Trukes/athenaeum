<?php

namespace Aedart\Tests\TestCases\Streams;

use Aedart\Config\Providers\ConfigLoaderServiceProvider;
use Aedart\Config\Traits\ConfigLoaderTrait;
use Aedart\Contracts\Config\Loaders\Exceptions\InvalidPathException;
use Aedart\Contracts\Config\Parsers\Exceptions\FileParserException;
use Aedart\Contracts\Streams\Locks\Lock;
use Aedart\Contracts\Streams\Stream as StreamInterface;
use Aedart\Streams\FileStream;
use Aedart\Streams\Providers\StreamServiceProvider;
use Aedart\Streams\Stream;
use Aedart\Streams\Traits\LockFactoryTrait;
use Aedart\Support\Helpers\Config\ConfigTrait;
use Aedart\Testing\TestCases\LaravelTestCase;
use Codeception\Configuration;

/**
 * Stream Test-Case
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\TestCases\Streams
 */
abstract class StreamTestCase extends LaravelTestCase
{
    use ConfigLoaderTrait;
    use ConfigTrait;
    use LockFactoryTrait;

    /*****************************************************************
     * Setup Methods
     ****************************************************************/

    /**
     * {@inheritdoc}
     *
     * @throws InvalidPathException
     * @throws FileParserException
     */
    protected function _before()
    {
        parent::_before();

        $this->getConfigLoader()
            ->setDirectory($this->configDir())
            ->load();
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            ConfigLoaderServiceProvider::class,
            StreamServiceProvider::class
        ];
    }

    /**
     * Returns the path to configuration files
     *
     * @return string
     */
    public function configDir(): string
    {
        return Configuration::dataDir() . 'configs/streams';
    }

    /**
     * Returns path to test files
     *
     * @return string
     */
    public function filesDir(): string
    {
        return Configuration::dataDir() . 'streams';
    }

    /*****************************************************************
     * Helpers
     ****************************************************************/

    /**
     * Returns full path to file
     *
     * @param  string  $file
     *
     * @return string
     */
    public function filePath(string $file): string
    {
        return $this->filesDir() . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Returns a stream for a "text" testing file
     *
     * @param  string  $mode  [optional]
     *
     * @return Stream
     *
     * @throws \Aedart\Contracts\Streams\Exceptions\StreamException
     */
    public function makeTextFileStream(string $mode = 'rb'): Stream
    {
        $path = $this->filePath('text.txt');

        return Stream::make(fopen($path, $mode));
    }

    /**
     * Open a "file stream" for given file
     *
     * @param  string  $file
     * @param  string  $mode  [optional]
     *
     * @return FileStream
     *
     * @throws \Aedart\Contracts\Streams\Exceptions\StreamException
     */
    public function openFileStreamFor(string $file, string $mode = 'rb'): FileStream
    {
        $path = $this->filePath($file);

        return FileStream::open($path, $mode);
    }

    /**
     * Creates a new lock instance for given stream
     *
     * @param  StreamInterface  $stream
     * @param  string|null  $profile  [optional]
     * @param  array  $options  [optional]
     *
     * @return Lock
     *
     * @throws \Aedart\Contracts\Streams\Exceptions\LockException
     */
    public function makeLock(StreamInterface $stream, ?string $profile = null, array $options = []): Lock
    {
        return $this->getLockFactory()->create($stream, $profile, $options);
    }
}
