<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.subscription.organizations'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('org_id');
            $table->integer('subsciption_id');

            //Subscription and Transaction details
            $table->unsignedBigInteger('country_id')->nullable();
            $table->decimal('amount_payable', 10, 4)->default(0);
            $table->decimal('amount_paid', 10, 4)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->text('data_json')->nullable();
            $table->string('invoice_id')->nullable();
            $table->text('txn_data_json')->nullable();

            $table->timestamp('start_at')->nullable();
            $table->timestamp('expiry_at')->nullable();
            $table->boolean('is_active')->default(false);

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.subscription.organizations'));
    }
}
