<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_types_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone_no');
            $table->string('phone_no_1')->nullable();
            $table->string('citizenship_no')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('license_no')->nullable();
            $table->string('permanent_address');
            $table->string('temporary_address')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
