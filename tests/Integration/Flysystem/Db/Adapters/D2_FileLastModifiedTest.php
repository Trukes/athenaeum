<?php

namespace Aedart\Tests\Integration\Flysystem\Db\Adapters;

use Aedart\Tests\TestCases\Flysystem\Db\FlysystemDbTestCase;

/**
 * D2_FileLastModifiedTest
 *
 * @group flysystem
 * @group flysystem-db
 * @group flysystem-db-d2
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Tests\Integration\Flysystem\Db\Adapters
 */
class D2_FileLastModifiedTest extends FlysystemDbTestCase
{
    /**
     * @test
     *
     * @return void
     *
     * @throws \League\Flysystem\FilesystemException
     */
    public function canSetAndObtainLastModifiedTimestamp(): void
    {
        $path = 'home/books/october_falls.txt';
        $content = $this->getFaker()->sentence();

        // ----------------------------------------------------------------- //

        $time = now()->subDays(3)->timestamp;

        $fs = $this->filesystem();
        $fs->write($path, $content, [
            'timestamp' => $time
        ]);

        // ----------------------------------------------------------------- //

        $result = $fs->lastModified($path);

        $this->assertSame($time, $result);
    }
}