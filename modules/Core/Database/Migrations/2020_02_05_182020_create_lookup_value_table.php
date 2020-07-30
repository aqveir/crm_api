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
        Schema::create(config('crmomni-migration.table_name.lookup_value'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id')->default(0);
            $table->integer('lookup_id');
            $table->string('key')->unique();
            $table->string('display_value')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists(config('crmomni-migration.table_name.lookup_value'));
    }
}
