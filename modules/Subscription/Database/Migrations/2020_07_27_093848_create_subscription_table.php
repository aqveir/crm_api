<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionTable extends Migration
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

            //Suggested Preferences
            $table->unsignedBigInteger('upgrade_id')->nullable();

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();


            // FROM Cashier migrations
            //
            // $table->bigIncrements('id');
            // $table->unsignedBigInteger('user_id');
            // $table->string('name');
            // $table->string('stripe_id');
            // $table->string('stripe_status');
            // $table->string('stripe_price')->nullable();
            // $table->integer('quantity')->nullable();
            // $table->timestamp('trial_ends_at')->nullable();
            // $table->timestamp('ends_at')->nullable();
            // $table->timestamps();

            // $table->index(['user_id', 'stripe_status']);
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
