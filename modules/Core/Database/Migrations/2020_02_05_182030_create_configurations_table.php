<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.configuration.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('type_id');
            $table->string('key', 255)->unique();
            $table->string('display_value', 255)->nullable();
            $table->text('filter')->nullable();
            $table->text('schema')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create(config('aqveir-migration.table_name.configuration.industry'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('configuration_id');
            $table->integer('industry_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.configuration.main'));

        Schema::dropIfExists(config('aqveir-migration.table_name.configuration.industry'));
    }
}
