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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('uqc_code', 20)->nullable(); // GST Unique Quantity Code (e.g. CMS, MTR, KGS, GMS, BGS)
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('is_base_unit')->default(true);
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->string('operator', 5)->default('/'); // '/' -> X [Unit] = 1 [BaseUnit] (100 CM = 1 M), '*' -> 1 [Unit] = X [BaseUnit] (1 BAG = 20 KG)
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active'); // active / inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('code');
            $table->index('base_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
