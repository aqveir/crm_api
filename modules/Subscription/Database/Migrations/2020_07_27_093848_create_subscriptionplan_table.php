<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionplanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.subscription.main'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('key', 255)->unique();
            $table->string('display_value', 255);
            $table->string('description', 1000)->nullable();
            $table->text('data_json')->nullable();

            $table->integer('order')->default(0);
            $table->boolean('is_displayed')->default(true);
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
        Schema::dropIfExists(config('crmomni-migration.table_name.subscription.main'));
    }
}
