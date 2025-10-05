<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLookupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.lookup'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('key')->unique();
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('is_editable')->default(true);

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
        Schema::dropIfExists(config('aqveir-migration.table_name.lookup'));
    }
}
