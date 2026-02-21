<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @author Kuldeep
     */
    public function up(): void
    {
        Schema::create('user_company_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'company_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @author Kuldeep
     */
    public function down(): void
    {
        Schema::dropIfExists('user_company_role');
    }
};
