<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.contact.details'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('subtype_id')->nullable();

            $table->string('identifier')->nullable();
            $table->string('proxy')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
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
        Schema::dropIfExists(config('aqveir-migration.table_name.contact.details'));
    }
}
