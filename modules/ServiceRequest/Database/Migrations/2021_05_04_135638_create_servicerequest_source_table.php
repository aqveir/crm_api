<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicerequestSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.service_request.source'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //SR Preference Relationship Core References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('servicerequest_id');

            $table->string('source_key');
            $table->string('display_value');

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
        Schema::dropIfExists(config('crmomni-migration.table_name.service_request.source'));
    }
}
