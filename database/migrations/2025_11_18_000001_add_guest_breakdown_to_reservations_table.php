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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'adults')) {
                $table->unsignedSmallInteger('adults')->default(0)->after('guests');
            }
            if (!Schema::hasColumn('reservations', 'children')) {
                $table->unsignedSmallInteger('children')->default(0)->after('adults');
            }
            if (!Schema::hasColumn('reservations', 'pets')) {
                $table->unsignedSmallInteger('pets')->default(0)->after('children');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $drops = [];
            foreach (['adults','children','pets'] as $col) {
                if (Schema::hasColumn('reservations', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
