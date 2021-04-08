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

            $table->unsignedBigInteger('org_id');
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->string('avatar', 4000)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();

            //To be used for 2FA/TOTP based authentication
            $table->string('2fa_secret', 1000)->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->integer('timezone_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();            
            
            $table->rememberToken();
            $table->dateTime('last_login_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_remote_access_only')->default(false);
            $table->integer('failed_attempts')->default(0);
            $table->integer('max_failed_attempts')->default(0);

            //Verification
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->timestamp('verified_at')->nullable();

            //Preferences
            $table->string('language')->default('en');

            //Fields for Tele calling & Activity allocation
            $table->string('virtual_phone_number')->nullable(); //Tele caller virtual number      
            $table->boolean('is_pool')->default(false);     //Pool user for assignment of system activities
            $table->boolean('is_default')->default(false);  //Default user created for an organization

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('crmomni-migration.table_name.user.availability'), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('user_id');
            $table->integer('status_id');
            $table->timestamps();
            $table->ipAddress('ip_address')->nullable();
        });

        Schema::create(config('crmomni-migration.table_name.user.availability_history'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->integer('status_id');
            $table->timestamp('created_at')->useCurrent();
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
