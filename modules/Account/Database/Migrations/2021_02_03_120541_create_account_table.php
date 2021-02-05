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
        Schema::create(config('crmomni-migration.table_name.account.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('type_id'); //e.g. Internal/External/etc

            //Account Address
            $table->string('address')->nullable();
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('zipcode')->nullable();

            //Location Details
            $table->integer('google_place_id')->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();            
            
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_default')->default(false);  //Default user created for an organization

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
        Schema::dropIfExists(config('crmomni-migration.table_name.account.main'));
    }
}
