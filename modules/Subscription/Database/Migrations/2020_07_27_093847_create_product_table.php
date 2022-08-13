<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.subscription.plan'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('product_uuid');
            $table->string('pricings');

            $table->string('display_value', 255);
            $table->text('meta_data')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);

            //Upgrade recommendation
            $table->unsignedBigInteger('upgrade_product_id')->default(0);

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
        Schema::dropIfExists(config('aqveir-migration.table_name.subscription.plan'));
    }
}
