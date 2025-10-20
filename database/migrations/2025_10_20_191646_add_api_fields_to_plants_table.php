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
        Schema::table('plants', function (Blueprint $table) {
            $table->integer('api_id')->unique()->after('id');
            $table->string('scientific_name')->nullable()->after('common_name');
            $table->string('family')->nullable()->after('scientific_name');
            $table->string('origin')->nullable()->after('family');
            $table->json('default_image')->nullable()->after('origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn(['api_id', 'scientific_name', 'family', 'origin', 'default_image']);
        });
    }
};
