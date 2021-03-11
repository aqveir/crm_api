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
        Schema::create(config('crmomni-migration.table_name.subscription.organizations'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('org_id');
            $table->integer('subsciption_id');

            //Subscription and Transaction details
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expiry_at')->nullable();
            $table->text('data_json')->nullable();
            $table->boolean('is_active')->default(false);

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
        Schema::dropIfExists(config('crmomni-migration.table_name.subscription.organizations'));
    }
}
