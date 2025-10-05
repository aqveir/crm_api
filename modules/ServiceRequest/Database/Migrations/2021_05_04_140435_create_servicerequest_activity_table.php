<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicerequestActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.service_request.activity'), function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id');

            //SR Activity Relationship Core References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('servicerequest_id');

            //SR Activity Relationship Keys
            $table->unsignedBigInteger('type_id');                  // i.e. Task, Event
            $table->unsignedBigInteger('subtype_id')->nullable();

            //SR Activity Common Attributes
            $table->string('subject', 255);
            $table->string('description', 4000)->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();            

            //Task Specific Attributes
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->timestamp('completed_at')->nullable();

            //Event Specific Attributes
            $table->string('location', 4000)->nullable();

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->ipAddress('ip_address')->nullable();
        });

        //Activity: Event Participants
        Schema::create(config('aqveir-migration.table_name.service_request.activity_participants'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('participant_type_id');
            $table->unsignedBigInteger('participant_id');

            //Task Specific Attributes
            $table->timestamp('completed_at')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.activity'));

        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.activity_participants'));
    }
}
