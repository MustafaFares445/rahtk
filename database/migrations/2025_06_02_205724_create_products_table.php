<?php

use App\Enums\ProductTypes;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->bigInteger('price')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->bigInteger('discount')->nullable();
            $table->unsignedBigInteger('view')->default(0);
            $table->string('address')->nullable();
            $table->enum('type', array_column(ProductTypes::cases(), 'value'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
