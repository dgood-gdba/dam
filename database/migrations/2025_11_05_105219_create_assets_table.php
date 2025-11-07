<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', static function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('file_name')->index();
            $table->enum('file_type', ['image', 'video', 'document', 'audio']);
            $table->bigInteger('file_size');
            $table->string('mime_type')->nullable();
            $table->string('extension')->nullable();
            $table->string('path')->unique()->index();
            $table->foreignId('directory_id')->nullable()->constrained('directories');
            $table->timestamps();
        });
    }
};
