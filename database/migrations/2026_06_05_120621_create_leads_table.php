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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('website_url')->nullable();
            $table->string('phone_number');
            $table->enum('status', ['pending', 'researching', 'compressing', 'drafting', 'queued', 'sending', 'sent', 'failed'])->default('pending');
            $table->longText('raw_research_data')->nullable();
            $table->text('compressed_context')->nullable();
            $table->text('generated_copy')->nullable();
            $table->text('error_logs')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
