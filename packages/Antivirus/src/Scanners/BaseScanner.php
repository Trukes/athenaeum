<?php

namespace Aedart\Antivirus\Scanners;

use Aedart\Antivirus\Events\FileWasScanned as FileWasScannedEvent;
use Aedart\Contracts\Antivirus\Events\FileWasScanned;
use Aedart\Contracts\Antivirus\Exceptions\UnsupportedStatusValueException;
use Aedart\Contracts\Antivirus\Results\ScanResult;
use Aedart\Contracts\Antivirus\Results\Status;
use Aedart\Contracts\Antivirus\Scanner;
use Aedart\Contracts\Streams\FileStream;
use Aedart\Support\Helpers\Events\DispatcherTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

/**
 * Base Scanner
 *
 * Abstraction for antivirus scanner
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Antivirus\Scanners
 */
abstract class BaseScanner implements Scanner
{
    use DispatcherTrait;

    /**
     * Creates a new antivirus scanner instance
     *
     * @param Dispatcher|null $dispatcher [optional]
     * @param array $options [optional]
     */
    public function __construct(
        Dispatcher|null $dispatcher = null,
        protected array $options = []
    ) {
        $this->setDispatcher($dispatcher);
    }

    /**
     * @inheritDoc
     */
    public function scan(string|SplFileInfo|FileStream|StreamInterface $file): ScanResult
    {
        // TODO: Add a default try-catch implementation that invokes a "scanFile" method...
    }

    /**
     * @inheritDoc
     */
    public function isClean(string|SplFileInfo|FileStream|StreamInterface $file): bool
    {
        return $this->scan($file)->isOk();
    }

    /**
     * Get an "item" from this scanner's options
     *
     * @param string|int|array|null $key
     * @param mixed $default [optional]
     *
     * @return mixed
     */
    public function get(string|int|array|null $key, mixed $default = null): mixed
    {
        return data_get($this->options, $key, $default);
    }

    /*****************************************************************
     * Abstract methods
     ****************************************************************/

    /**
     * Return class path to file scan status to be used
     *
     * @return class-string<Status>
     */
    abstract protected function scanStatus(): string;

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Creates a new file scan status instance
     *
     * @param mixed $value
     * @param string|null $reason [optional]
     *
     * @return Status
     *
     * @throws UnsupportedStatusValueException
     */
    protected function makeScanStatus(mixed $value, string|null $reason = null): Status
    {
        $class = $this->scanStatus();

        return $class::make($value, $reason);
    }

    /**
     * Dispatches "file was scanned" event with given scan result
     *
     * @param ScanResult $result
     *
     * @return void
     */
    protected function dispatchFileWasScanned(ScanResult $result): void
    {
        $event = $this->makeFileWasScannedEvent($result);

        $this->getDispatcher()->dispatch(FileWasScanned::class, $event);
    }

    /**
     * Creates a new "file was scanned" event instance
     *
     * @param ScanResult $result
     *
     * @return FileWasScanned
     */
    protected function makeFileWasScannedEvent(ScanResult $result): FileWasScanned
    {
        return new FileWasScannedEvent($result);
    }
}