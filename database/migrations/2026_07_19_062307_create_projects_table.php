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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('district')->nullable();
            $table->string('address')->nullable()->comment('Địa chỉ theo đơn vị hành chính sau sáp nhập');
            $table->string('former_address')->nullable()->comment('Địa chỉ trước sáp nhập (tham chiếu)');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('status')->default('upcoming')->index();
            $table->timestamp('application_start_at')->nullable();
            $table->timestamp('application_end_at')->nullable();
            $table->unsignedInteger('total_units')->nullable();
            $table->unsignedBigInteger('price_from')->nullable();
            $table->unsignedBigInteger('price_to')->nullable();
            $table->unsignedInteger('area_from')->nullable();
            $table->unsignedInteger('area_to')->nullable();
            $table->longText('description')->nullable();
            $table->text('application_guide')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_name')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
