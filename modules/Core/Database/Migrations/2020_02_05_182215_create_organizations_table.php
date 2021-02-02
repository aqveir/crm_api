<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.organizations'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->string('name');
            $table->string('subdomain');
            $table->string('custom_domain')->nullable();

            //Organization Attributes
            $table->string('logo')->nullable();
            $table->integer('industry_id'); //e.g. Retail/Travel/ etc
            $table->integer('timezone_id')->nullable();

            //Organization Address
            $table->string('address')->nullable();
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('zipcode')->nullable();

            //Location Details
            $table->integer('google_place_id')->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();            
            
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_active')->default(true);

            //Audit Log Fields
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('crmomni-migration.table_name.organizations'));
    }
}
