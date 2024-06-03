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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('nik')->unique();
            $table->string('address');
            $table->foreignId(config('laravolt.indonesia.table_prefix') . 'province_id')->constrained();
            $table->foreignId(config('laravolt.indonesia.table_prefix') . 'city_id')->constrained();
            $table->foreignId(config('laravolt.indonesia.table_prefix') . 'district_id')->constrained();
            $table->foreignId(config('laravolt.indonesia.table_prefix') . 'village_id')->constrained();
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
        Schema::dropIfExists('customers');
    }
};
