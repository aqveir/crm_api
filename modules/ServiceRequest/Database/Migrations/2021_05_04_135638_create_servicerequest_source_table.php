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
        Schema::create(config('aqveir-migration.table_name.service_request.source'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //SR Source References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('channel_type_id');

            $table->string('identifier');

            $table->timestamps();
        });


        Schema::create(config('aqveir-migration.table_name.service_request.source-data'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //SR Sources Mapper References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('servicerequest_id');
            $table->unsignedBigInteger('source_id');

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
        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.source'));

        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.source-data'));
    }
}
