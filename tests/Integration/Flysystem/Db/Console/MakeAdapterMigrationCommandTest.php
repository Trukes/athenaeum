<?php

namespace Aedart\Tests\Integration\Flysystem\Db\Console;

use Aedart\Testing\Helpers\ConsoleDebugger;
use Aedart\Tests\TestCases\Flysystem\Db\FlysystemDbTestCase;
use Aedart\Utils\Str;
use Codeception\Configuration;

/**
 * MakeAdapterMigrationCommandTest
 *
 * @group flysystem
 * @group flysystem-db
 * @group flysystem-db-console
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Tests\Integration\Flysystem\Db\Console
 */
class MakeAdapterMigrationCommandTest extends FlysystemDbTestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function commandIsRegisteredInArtisan()
    {
        $this
            ->artisan(self::MAKE_MIGRATION_CMD, [
                '-h'
            ])
            ->assertSuccessful();
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws \Codeception\Exception\ConfigurationException
     */
    public function canCreateDefaultAdapterMigrationFile()
    {
        $this
            ->artisan(self::MAKE_MIGRATION_CMD, [
                '--type' => 'default',
                '--name' => 'files',
                '--path' => $this->migrationsPath()
            ])
            ->assertSuccessful();

        // ------------------------------------------------------------------------------------- //

        $fs = $this->getFile();
        $files = $fs->files($this->outputDir());

        ConsoleDebugger::output($files);

        $this->assertNotEmpty($files, 'No migration files published');

        // ------------------------------------------------------------------------------------- //

        $wasCreated = false;
        foreach ($files as $file) {
            if (Str::endsWith($file->getBasename(), 'create_files_table.php')) {
                $wasCreated = true;
                break;
            }
        }

        $this->assertTrue($wasCreated, 'Migration file does not appear to be created by command');
    }
}