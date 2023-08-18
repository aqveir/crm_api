<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.contact.main'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->unsignedBigInteger('org_id');
            //$table->string('username')->unique();
            //$table->string('password')->nullable();

            //Social login - Socialite
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->text('avatar')->nullable();

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->dateTime('birth_at')->nullable();

            //Contact Relationship Keys
            $table->unsignedBigInteger('gender_id')->nullable();        //E.g. Male, Female, Others
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();          //E.g. Whole-seller, Distributor

            //Provision to keep industry specific data
            $table->json('extras')->nullable();

            //Setting data for the contact used by the CRMO
            $table->json('settings')->nullable();

            //Search tags for the contact
            $table->string('search_tags')->nullable();

            $table->rememberToken();
            $table->boolean('allow_login')->default(false);
            $table->dateTime('last_login_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('failed_attempts')->default(0);
            $table->integer('max_failed_attempts')->default(0);

            //Referral Fields
            $table->string('referral_code')->nullable();
            $table->integer('referred_by')->nullable();

            //Preferences
            $table->string('language_code')->default('en');
            $table->unsignedBigInteger('timezone_id')->nullable();
            
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
        Schema::dropIfExists(config('aqveir-migration.table_name.contact.main'));
    }
}
