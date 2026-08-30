<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 2 stub (working-capital facility). Schema reserved now; the
     * application/approval/disbursement workflow is built later, behind
     * the credit-engine feature flag, once a partner bank/MFI is in place.
     */
    public function up(): void
    {
        Schema::create('loan_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->decimal('requested_amount', 14, 2)->nullable();
            $table->decimal('approved_amount', 14, 2)->nullable();
            $table->string('status')->default('draft');
            // draft, applied, approved, declined, disbursed, closed
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_facilities');
    }
};
