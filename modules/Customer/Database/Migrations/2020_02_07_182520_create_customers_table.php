<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.customer.main'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->integer('org_id');
            //$table->string('username')->unique();
            $table->string('password');
            $table->string('last_otp')->nullable();

            //Social login - Socialite
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->text('avatar')->nullable();

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->dateTime('date_of_birth_at')->nullable();

            //Customer Relationship Keys
            $table->integer('occupation_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('gender_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('status_id')->nullable();

            $table->rememberToken();
            $table->dateTime('last_login_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('failed_attempts')->default(0);
            $table->integer('max_failed_attempts')->default(0);

            //Referral Fields
            $table->string('referral_code')->nullable();
            $table->integer('referred_by')->nullable();
            
            //Audit Log Fields
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            
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
        Schema::dropIfExists(config('crmomni-migration.table_name.customer.main'));
    }
}
