<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicerequestPreferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.service_request.preference'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //SR Preference Relationship Core References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('servicerequest_id');

            //SR Preference attributes
            $table->unsignedBigInteger('preference_id');
            $table->string('preference_value')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.preference'));
    }
}
