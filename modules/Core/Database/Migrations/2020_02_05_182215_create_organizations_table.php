<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('omnicrm-migration.table_name.organizations'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->string('name')->nullable();
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
        Schema::dropIfExists(config('omnicrm-migration.table_name.organizations'));
    }
}
