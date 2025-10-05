<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.currencies'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //Currency details
            $table->string('iso_code')->nullable();
            $table->string('iso_digit')->nullable();
            $table->string('display_value', 255)->nullable();
            $table->string('symbol')->nullable();
            $table->boolean('is_symbol_left_pos')->default(true);
            $table->integer('decimal_places')->default(2);
            $table->decimal('fx_rate', 10, 4)->default(1);

            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists(config('aqveir-migration.table_name.currencies'));
    }
}
