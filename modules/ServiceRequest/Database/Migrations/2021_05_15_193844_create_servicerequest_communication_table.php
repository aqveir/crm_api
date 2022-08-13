<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicerequestCommunicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.service_request.communication'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //SR Communication Essential References
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('servicerequest_id');
            $table->unsignedBigInteger('activity_subtype_id');
            $table->unsignedBigInteger('direction_id'); //Map to 'communication_direction' in lookup collection

            //Common Properties
            $table->string('external_uuid')->nullable(); //External Reference (i.e. Exotel SID)
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->unsignedBigInteger('from_person_type_id')->nullable();       //Initiator type: User/Contact Type Id
            $table->unsignedBigInteger('from_person_identifier_id')->nullable(); //Initiator Identifier: User/Contact Id
            $table->unsignedBigInteger('to_person_type_id')->nullable();       //Initiator type: User/Contact Type Id
            $table->unsignedBigInteger('to_initiator_identifier_id')->nullable(); //Initiator Identifier: User/Contact Id

            //Call Properties
            $table->string('call_from')->nullable();
            $table->string('call_to')->nullable();
            $table->unsignedBigInteger('call_status_id')->nullable();
            $table->integer('call_duration')->default(0);
            $table->string('call_recording_url', 4000)->nullable();
            $table->text('call_transcript')->nullable();

            //SMS Properties
            $table->string('sms_from')->nullable();
            $table->string('sms_to')->nullable();
            $table->string('sms_message', 2000)->nullable();

            //Email Properties
            $table->string('email_from')->nullable();
            $table->string('email_to')->nullable();
            $table->string('email_cc')->nullable();
            $table->string('email_subject', 255)->nullable();
            $table->text('email_body')->nullable();

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
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
        Schema::dropIfExists(config('aqveir-migration.table_name.service_request.communication'));
    }
}
