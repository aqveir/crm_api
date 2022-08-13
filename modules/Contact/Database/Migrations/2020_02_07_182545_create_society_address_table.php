<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocietyAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.contact.society_address'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');        
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('zipcode')->nullable();

            //Location Details
            $table->integer('google_place_id')->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();

            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    } //Function ends


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.contact.society_address'));
    } //Function ends
} //Class ends
