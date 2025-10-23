<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->text('origin')->change();
            $table->text('default_image')->change();
            $table->text('watering_general_benchmark')->change();
            $table->text('scientific_name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->string('origin', 191)->change();
            $table->string('default_image', 191)->change();
            $table->string('watering_general_benchmark', 191)->change();
            $table->string('scientific_name', 191)->change();
        });
    }
};