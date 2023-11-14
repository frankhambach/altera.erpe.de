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
        Schema::dropIfExists('territory_geometries');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('territory_geometries', function (Blueprint $table) {
            $table->id();
            $table->multiPolygon('land');
            $table->multiPolygon('sea')->nullable();
            $table->multiLineString('land_border')->nullable();
            $table->multiLineString('sea_border')->nullable();
            $table->multiLineString('coast')->nullable();
            $table->morphs('territory');
            $table->timestamps();
        });
    }
};
