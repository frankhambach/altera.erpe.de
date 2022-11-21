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
        Schema::create('federated_states', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('code', 2);
            $table->foreignId('federal_state_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['federal_state_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('federated_states');
    }
};
