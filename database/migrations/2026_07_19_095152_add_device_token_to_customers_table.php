<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // device_token එක text එකක් විදියට දානවා (මොකද firebase token එක ටිකක් දිගයි)
            $table->text('device_token')->nullable()->after('password');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('device_token');
        });
    }
};