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
        Schema::create(config('crmomni-migration.table_name.preference.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->string('key');
            $table->string('display_value')->nullable();
            $table->string('description')->nullable();
            $table->string('column_name')->nullable();
            $table->integer('type_id')->nullable();             //From Global Lookup Value Table
            $table->integer('data_id')->nullable();             //From Preference Data Table
            $table->boolean('is_multiple')->default(false);     //Allows multiple selection
            $table->string('keywords')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists(config('crmomni-migration.table_name.preference.main'));
    }
}
