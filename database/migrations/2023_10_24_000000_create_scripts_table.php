<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cultures', function (Blueprint $table) {
            $table->dropForeign(['script_id']);
            $table->dropColumn('script_id');
        });

        Schema::dropIfExists('script_sources');

        Schema::dropIfExists('scripts');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('code', 3);
            $table->enum('case', ['none', 'bicameral', 'ideocameral', 'unicameral']);
            $table->enum('fitting', ['none', 'continual', 'interpunctual', 'interspatial']);
            $table->enum('flow', ['none', 'linear', 'zig_zag']);
            $table->enum('format', ['none', 'columns', 'rows']);
            $table->enum('horizontal_orientation', ['none', 'left_to_right', 'right_to_left']);
            $table->string('name');
            $table->enum('type', ['none', 'abjad', 'abugida', 'alphabet', 'charactery', 'featurary', 'ideatary', 'syllabary']);
            $table->enum('vertical_orientation', ['none', 'downwards', 'upwards']);
            $table->timestamps();

            $table->unique(['slug']);
        });

        Schema::create('script_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->string('iso_code', 4)->nullable();
            $table->string('omniglot_code')->nullable();
            $table->bigInteger('wikidata_id')->nullable();
            $table->foreignId('script_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('cultures', function (Blueprint $table) {
            $table->foreignId('script_id')
                ->nullable()
                ->after('state_id')
                ->constrained()
                ->onDelete('set null');
        });
    }
};
