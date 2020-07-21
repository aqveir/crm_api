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
        Schema::create(config('omnicrm-migration.table_name.lookup_value'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id')->default(0);
            $table->integer('lookup_id');
            $table->string('key')->unique();
            $table->string('display_value')->nullable();
            $table->string('description')->nullable();
            $table->integer('order')->default(1);

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
        Schema::dropIfExists(config('omnicrm-migration.table_name.lookup_value'));
    }
}
