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
        Schema::table('matches', function (Blueprint $table) {
            $table->string('initiator_type')->nullable()->after('exporter_id'); // 'producer' or 'exporter'
            $table->unsignedBigInteger('initiator_id')->nullable()->after('initiator_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['initiator_type', 'initiator_id']);
        });
    }
};
