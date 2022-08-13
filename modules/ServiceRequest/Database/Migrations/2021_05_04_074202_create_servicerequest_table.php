<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicerequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.service_request.main'), function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');
            $table->string('hash')->nullable();
            $table->string('name')->nullable(); //Name for the SR/Lead

            //SR Relationship Core References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('owner_id');

            //SR Relationship Keys
            $table->unsignedBigInteger('category_id');      //i.e. Lead, Opprtunity, Support
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('stage_id');

            $table->string('search_tags')->nullable();
            $table->integer('star_rating')->default(0);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->ipAddress('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.main'));
    }
}
