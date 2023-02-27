<?php

namespace Aedart\Antivirus\Scanners;

use Aedart\Antivirus\Scanners\Status\ClamAvStatus;
use Aedart\Contracts\Antivirus\Exceptions\UnsupportedStatusValueException;
use Aedart\Contracts\Antivirus\Results\ScanResult;
use Aedart\Contracts\Streams\BufferSizes;
use Aedart\Contracts\Streams\FileStream;
use Socket\Raw\Factory as ConnectionFactory;
use Xenolope\Quahog\Client;
use Xenolope\Quahog\Exception\ConnectionException;
use Xenolope\Quahog\Result;

/**
 * ClamAV Scanner
 *
 * @see https://www.clamav.net/
 * @see
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Antivirus\Scanners
 */
class ClamAv extends BaseScanner
{
    /**
     * Destructor
     */
    public function __destruct()
    {
        if (!$this->hasDriver() || $this->isDriverMocked()) {
            return;
        }

        try {
            $this->driver()->disconnect();
        } catch (ConnectionException $e) {
            // Unable to do anything at this point. Throwing exceptions in
            // a destructor will just cause a fatal error...
        }
    }

    /**
     * @inheritDoc
     */
    public function scanStream(FileStream $stream): ScanResult
    {
        /** @var Client $driver */
        $driver = $this->driver();

        $nativeResult = $driver->scanResourceStream(
            stream: $stream->resource(),
            maxChunkSize: $this->chunkSize()
        );

        return $this->parseNativeResult($nativeResult, $stream);
    }

    /**
     * Returns the socket to connect to
     *
     * @return string
     */
    public function socket(): string
    {
        return $this->get('socket', '/var/run/clamav/clamd.ctl');
    }

    /**
     * Returns socket connection timeout
     *
     * @return int|null Seconds
     */
    public function socketTimeout(): int|null
    {
        return $this->get('socket_timeout', 2);
    }

    /**
     * Returns timeout for obtaining scan results
     *
     * @return int Seconds
     */
    public function timeout(): int
    {
        return $this->get('timeout', 30);
    }

    /**
     * Returns the maximum amount of bytes to send to ClamAV, in a single chunk
     *
     * This value SHOULD NOT exceed "StreamMaxLength", defined in your clamd.conf (default 25 Mb).
     *
     * Behind the scene, PHP's {@see socket_send()} method is used. The higher the
     * value, the faster data will be sent to ClamAV, but the more memory is used
     * by PHP.
     *
     * @see https://manpages.ubuntu.com/manpages/jammy/man5/clamd.conf.5.html
     * @see https://www.php.net/manual/en/function.socket-send
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return $this->get('chunk_size', BufferSizes::BUFFER_1MB * 10);
    }

    /**
     * Parses native result and converts it to a scan result instance
     *
     * @param Result $native
     * @param FileStream $stream
     *
     * @return ScanResult
     *
     * @throws UnsupportedStatusValueException
     */
    protected function parseNativeResult(Result $native, FileStream $stream): ScanResult
    {
        $status = $this->makeScanStatus($native, $native->getReason());

        return $this->makeScanResult(
            status: $status,
            file: $stream,
            details: [
                'clamav_session_id' => $native->getId()
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function statusClass(): string
    {
        return ClamAvStatus::class;
    }

    /**
     * @inheritDoc
     */
    protected function makeDriver(): Client
    {
        $connection = (new ConnectionFactory())->createClient(
            address: $this->socket(),
            timeout: $this->socketTimeout()
        );

        return new Client(
            socket: $connection,
            timeout: $this->timeout(),
            mode: PHP_NORMAL_READ
        );
    }
}
