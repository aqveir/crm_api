<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.notes'), function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK Relationships
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('entity_type_id');
            $table->unsignedBigInteger('reference_id');
            $table->text('note')->nullable();

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
        Schema::dropIfExists(config('aqveir-migration.table_name.notes'));
    }
}
