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
       Schema::create('measurement_requests', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->json('measurements'); 
        $table->text('notes')->nullable();
        $table->string('status')->default('pending'); 
        $table->timestamps();

        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_requests');
    }
};
