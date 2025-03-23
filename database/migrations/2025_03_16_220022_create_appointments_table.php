<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('appointmentId')->nullable();
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->string('reason')->nullable();
            $table->string('additional_details')->nullable();
            $table->string('status')->default('Pending')->comment('Pending, Confirmed, Rescheduled, Cancelled');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
