<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_outbox', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained('newsletters')->cascadeOnDelete();
            $table->foreignId('newsletter_subscriber_id')->nullable()->constrained('newsletter_subscribers')->nullOnDelete();
            $table->string('email');
            $table->string('status', 20)->default('pending');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'id']);
            $table->index(['newsletter_id', 'status']);
            $table->unique(['newsletter_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_outbox');
    }
};
