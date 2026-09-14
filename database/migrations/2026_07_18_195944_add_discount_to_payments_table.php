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
        Schema::table('payments', function (Blueprint $table) {
        // 🟢 Discount එක දාන්න (Optional නිසා nullable කරලා, default 0 දාලා තියෙන්නේ)
        $table->decimal('discount', 10, 2)->nullable()->default(0)->after('amount');
    });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('discount');
        });
    }
};
