<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferenceDataValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.preference.data_value'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('data_id');             //From Preference Data Table
            $table->string('value', 255);
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.preference.data_value'));
    }
}
