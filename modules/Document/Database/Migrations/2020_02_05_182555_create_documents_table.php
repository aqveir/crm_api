<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('aqveir-migration.table_name.documents'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash')->nullable();

            // FK Relationships
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('entity_type_id');
            $table->unsignedBigInteger('reference_id');

            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('file_path', 1000);
            $table->string('file_name', 100)->nullable();
            $table->string('file_extn', 20)->nullable();
            $table->decimal('file_size_in_kb', 10, 2)->default(0); // In Kilo Bytes
            $table->boolean('is_full_path')->default(false);
            $table->boolean('is_active')->default(true);

            //Document Attributes
            $table->string('author', 255)->nullable();
            $table->dateTime('expiry_at')->nullable();

            //Audit Log Fields
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->ipAddress('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('aqveir-migration.table_name.documents'));
    }
}
