<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.user.registration'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');

            //Verification
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);

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
        Schema::dropIfExists(config('aqveir-migration.table_name.user.registration'));
    }
}
