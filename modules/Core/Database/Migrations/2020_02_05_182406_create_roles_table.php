<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.roles'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('org_id');
            $table->string('key', 255);
            $table->string('display_value', 255)->nullable();
            $table->string('description', 1000)->nullable();
            $table->boolean('is_secure')->default(false);
            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.roles'));
    }
}
