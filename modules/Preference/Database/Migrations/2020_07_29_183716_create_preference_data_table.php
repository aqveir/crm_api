<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferenceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.preference.data'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->string('key', 255);
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();

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
        Schema::dropIfExists(config('crmomni-migration.table_name.preference.data'));
    }
}
