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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('producers', function (Blueprint $table) {
            if (!Schema::hasColumn('producers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('exporters', function (Blueprint $table) {
            if (!Schema::hasColumn('exporters', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('producers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('exporters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
