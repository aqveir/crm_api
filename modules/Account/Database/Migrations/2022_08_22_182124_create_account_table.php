<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.account.main'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->unsignedBigInteger('org_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('type_id'); //e.g. Internal/External/Etc
            $table->unsignedBigInteger('owner_id')->nullable();

            //Account Address
            $table->string('address')->nullable();
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('zipcode')->nullable();
            $table->unsignedBigInteger('timezone_id')->nullable();

            //Location Details
            $table->string('google_place_id')->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();            
            
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_default')->default(false);  //Default user created for an organization

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
        Schema::dropIfExists(config('aqveir-migration.table_name.account.main'));
    }

} //Function ends
