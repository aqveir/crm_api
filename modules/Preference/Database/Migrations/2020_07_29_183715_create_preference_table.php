<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.preference.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('org_id');
            $table->string('name', 255);
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('column_name', 255)->nullable();
            $table->unsignedBigInteger('type_id')->nullable();  //From Global Lookup Value Table
            $table->unsignedBigInteger('data_id')->nullable();  //From Preference Data Table
            $table->boolean('is_minimum')->default(false);      //Minimum data type
            $table->boolean('is_maximum')->default(false);      //Maximum data type
            $table->boolean('is_multiple')->default(false);     //Allows multiple selection
            $table->string('external_url', 4000)->nullable();   //External URL for TypeId=External Data
            $table->string('keywords', 4000)->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.preference.main'));
    }
}
