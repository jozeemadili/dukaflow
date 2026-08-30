<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('business_name');
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('new');
            // new, contacted, onboarding, converted, lost
            $table->text('notes')->nullable();
            $table->foreignId('converted_merchant_id')->nullable()->constrained('merchants')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
