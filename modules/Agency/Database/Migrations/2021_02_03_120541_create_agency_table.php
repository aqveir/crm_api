<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.agency.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->string('name');
            $table->integer('type_id'); //e.g. Retail/Travel/ etc

            $table->integer('country_id')->nullable();
            $table->integer('timezone_id')->nullable();
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
        Schema::dropIfExists(config('crmomni-migration.table_name.agency.main'));
    }
}
