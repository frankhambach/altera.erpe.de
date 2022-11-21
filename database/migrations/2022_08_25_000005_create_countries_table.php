<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('code', 3);
            $table->string('capital');
            $table->foreignId('area_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('region_id')
                ->constrained()
                ->onDelete('restrict');
            $table->morphs('state');
            $table->timestamps();

            $table->unique(['code']);
            $table->unique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
