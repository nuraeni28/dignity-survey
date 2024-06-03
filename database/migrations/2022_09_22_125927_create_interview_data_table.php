<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['essai', 'option', 'multiple', 'rating'])->default('essai');
            $table->text('answer')->nullable();
            $table->text('customer_answer')->nullable();
            $table->boolean('highlight')->default(false);
            $table->foreignId('question_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interview_data');
    }
};