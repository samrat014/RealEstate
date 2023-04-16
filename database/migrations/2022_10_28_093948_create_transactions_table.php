<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('land_id')->constrained()->onDelete('cascade');
            $table->string('price_per_anna');
            $table->string('nepali_date');
            $table->enum('type', ['income', 'expenses'])->default('income');
            $table->string('income')->nullable();
            $table->string('expenses')->nullable();
            $table->string('total_paid_amount');
            $table->string('commission_rate')->nullable();
            $table->string('total_commission')->nullable();
            $table->string('photo')->nullable();
            $table->string('total_commision_after_rate')->nullable();
            $table->string('descriptions');
            $table->string('cheque_exchange_date')->nullable();
            $table->boolean('ischeque')->default(0);
            $table->string('cheque_no')->nullable();
            $table->boolean('notification_status')->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
