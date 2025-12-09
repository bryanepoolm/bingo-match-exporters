<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hs_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. "0804.40"
            $table->string('description');
            $table->string('chapter', 2); // e.g. "08"
            $table->string('section')->nullable();
            $table->string('parent_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index('parent_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_codes');
    }
};
