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
        Schema::create(config('aqveir-migration.table_name.organizations'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            $table->string('name');
            $table->string('subdomain')->index();
            $table->string('custom_domain')->nullable();

            //Organization Attributes
            $table->string('logo')->nullable();
            $table->integer('industry_id'); //e.g. Retail/Travel/ etc
            $table->integer('timezone_id')->nullable();
            $table->string('search_tags')->nullable();

            //Organization Address
            $table->string('address')->nullable();
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('zipcode')->nullable();

            //Location Details
            $table->integer('google_place_id')->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->decimal('latitude', 12, 8)->nullable();            
            
            $table->string('website')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->boolean('is_active')->default(true);

            //Stripe Fields
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.organizations'));
    }
}
