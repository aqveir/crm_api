<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.customer.details'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->integer('customer_id');
            $table->integer('type_id')->nullable();
            $table->integer('subtype_id')->nullable();

            $table->integer('country_id')->nullable();
            $table->string('identifier')->nullable();
            $table->string('proxy')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
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
        Schema::dropIfExists(config('crmomni-migration.table_name.customer.details'));
    }
}
