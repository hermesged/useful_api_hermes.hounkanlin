<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('original_url');
            $table->string('code', 10)->unique();
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('short_links');
    }
};