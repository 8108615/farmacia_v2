
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor_secuencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ultimo_numero')->default(0);
        });

        DB::table('proveedor_secuencias')->insert([
            'id' => 1,
            'ultimo_numero' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor_secuencias');
    }
};
