<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimezoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('crmomni-migration.table_name.timezones'), function (Blueprint $table) {
            $table->bigIncrements('id');

            //Time zone details
            $table->integer('country_id');
            $table->string('iso3_code')->unique();
            $table->string('display_value', 255)->nullable();

            //GMT Offset
            $table->integer('gmt_offset')->default(0);
            $table->boolean('is_dst_enabled')->default(false);
            $table->datetime('dst_start_at')->nullable();
            $table->datetime('dst_end_at')->nullable();

            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists(config('crmomni-migration.table_name.timezones'));
    }
}
