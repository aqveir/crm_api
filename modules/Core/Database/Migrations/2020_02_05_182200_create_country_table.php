<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('omnicrm-migration.table_name.countries'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('alpha2_code')->unique();
            $table->string('alpha3_code')->nullable();
            $table->string('numeric_code')->nullable();
            $table->string('iso3166_2_code')->nullable();

            $table->string('display_value')->nullable();
            $table->string('display_official_name')->nullable();

            //Official domain extention
            $table->string('official_domain_extn')->nullable();

            //Currency details
            $table->string('currency_code')->nullable();

            //Phone details
            $table->string('phone_idd_code')->nullable();

            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();

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
        Schema::dropIfExists(config('omnicrm-migration.table_name.countries'));
    }
}
