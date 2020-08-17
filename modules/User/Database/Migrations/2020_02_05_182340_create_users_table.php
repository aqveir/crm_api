<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.user.main'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->integer('org_id');
            $table->string('username')->unique();
            $table->string('password');
            //$table->string('remember_token')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            //To be used for OTP based authentication
            $table->string('last_otp')->nullable();

            $table->integer('country_id')->nullable();
            $table->integer('timezone_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();            
            
            $table->rememberToken();
            $table->dateTime('last_login_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_remote_access_only')->default(false);
            $table->integer('failed_attempts')->default(0);
            $table->integer('max_failed_attempts')->default(0);

            //Fields for Tele calling & Activity allocation
            $table->string('virtual_phone_number')->nullable(); //Tele caller virtual number      
            $table->boolean('is_pool')->default(false);     //Pool user for assignment of system activities
            $table->boolean('is_default')->default(false);  //Default user created for an organization

            //Audit Log Fields
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('crmomni-migration.table_name.user.availability'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('status_id');
            $table->timestamps();
        });

        Schema::create(config('crmomni-migration.table_name.user.availability_history'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('status_id');
            $table->timestamp('timestamp')->useCurrent();
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
        Schema::dropIfExists(config('crmomni-migration.table_name.user.main'));

        Schema::dropIfExists(config('crmomni-migration.table_name.user.availability'));

        Schema::dropIfExists(config('crmomni-migration.table_name.user.availability_history'));
    }
}
