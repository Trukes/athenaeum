<?php

use Aedart\Contracts\Flysystem\Db\RecordTypes;
use Aedart\Contracts\Flysystem\Visibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Table name where files and directories are to
     * be stored
     *
     * @var string
     */
    protected string $filesTable = 'files';

    /**
     * Table name of actual "file contents"
     *
     * @var string
     */
    protected string $contentsTable = 'file_contents';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->filesTable, function (Blueprint $table) {
            $table->id();

            $table
                ->enum('type', RecordTypes::ALLOWED)
                ->comment('Whether this is a file or directory');

            // Materialized Path pattern
            // @see https://dzone.com/articles/materialized-paths-tree-structures-relational-database
            $table
                ->string('path', 255)
                ->unique()
                ->comment('Unique path of file or directory');

            $table
                ->unsignedSmallInteger('level')
                ->default(0)
                ->comment('Depth level');

            $table
                ->bigInteger('file_size')
                ->default(0)
                ->comment('Filesize in bytes');

            // File mimetype (Note: max size is acc. to https://datatracker.ietf.org/doc/html/rfc4288#section-4.2)
            $table
                ->string('mime_type', 127)
                ->nullable()
                ->default(null)
                ->comment('File media type / mimetype');

            $table
                ->enum('visibility', Visibility::ALLOWED)
                ->default(Visibility::PRIVATE)
                ->comment('File or directory visibility');

            $table
                ->string('content_hash', 128)
                ->nullable()
                ->comment('Hash of file content');

            $table
                ->bigInteger('last_modified')
                ->default(0)
                ->comment('Unix timestamp of when file or directory was last modified');

            $table
                ->json('extra_metadata')
                ->nullable()
                ->default(null)
                ->comment('Evt. custom extra meta data about file or directory');

            $table->index('path');
            $table->index(['path', 'level']);
            $table->index('content_hash');
        });

        Schema::create($this->contentsTable, function (Blueprint $table) {
            $table->id();

            $table
                ->string('hash', 128)
                ->unique()
                ->comment('Hash of file content');

            $table
                ->integer('reference_count')
                ->default(0)
                ->comment('Amount of files that references this content');

            $table
                ->binary('contents')
                ->comment('File contents');

            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->filesTable);
        Schema::dropIfExists($this->contentsTable);
    }
};
