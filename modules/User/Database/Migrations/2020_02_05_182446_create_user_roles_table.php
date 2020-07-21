<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('omnicrm-migration.table_name.user.roles'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('user_id');
            $table->integer('role_id');
            $table->string('description')->nullable();
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
        Schema::dropIfExists(config('omnicrm-migration.table_name.user.roles'));
    }
}
