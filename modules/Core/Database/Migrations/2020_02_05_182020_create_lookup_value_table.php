<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLookupValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.lookup_value'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id')->default(0);
            $table->integer('lookup_id');
            $table->string('key')->unique();
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_editable')->default(true);
            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists(config('aqveir-migration.table_name.lookup_value'));
    }
}
