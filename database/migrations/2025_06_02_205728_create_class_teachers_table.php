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
        Schema::disableForeignKeyConstraints();

        Schema::create('class_teachers', function (Blueprint $table) {
            $table->foreignId('teacher_id')->references('id')->on('teachers');
            $table->foreignId('school_class_id')->references('id')->on('school_classes');
            $table->primary(['teacher_id', 'school_class_id']);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_teachers');
    }
};
