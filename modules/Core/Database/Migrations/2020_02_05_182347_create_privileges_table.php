<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.privileges'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('key', 255)->unique();
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_secure')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.privileges'));
    }
}
