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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('garment_category');
            $table->string('garment_name');
            
            // Upper Body Measurements
            $table->decimal('collar', 5, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('shoulder', 5, 2)->nullable();
            $table->decimal('sleeve_length', 5, 2)->nullable();
            $table->decimal('full_length', 5, 2)->nullable();
            $table->decimal('armhole', 5, 2)->nullable();
            $table->decimal('upper_waist', 5, 2)->nullable();
            
            // Lower Body Measurements
            $table->decimal('lower_waist', 5, 2)->nullable();
            $table->decimal('hip', 5, 2)->nullable();
            $table->decimal('thigh', 5, 2)->nullable();
            $table->decimal('outseam', 5, 2)->nullable();
            $table->decimal('inseam', 5, 2)->nullable();
            $table->decimal('bottom_hem', 5, 2)->nullable();
            $table->decimal('knee', 5, 2)->nullable();
            
            // Design & Fabric details
            $table->string('fabric_source');
            $table->string('fabric_name')->nullable();
            $table->string('design_image')->nullable();
            $table->text('styling_notes')->nullable();
            
            // Status එක මෙතන තියෙනවා - Error එක එන්නේ නැති වෙන්න!
            $table->string('status')->default('Measuring'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};