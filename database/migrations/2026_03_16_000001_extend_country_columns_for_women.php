<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend country columns from char(2) to varchar(10)
        // to support women's federation codes like ESF, DEF, ENF, etc.
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('country', 10)->change();
            $table->string('flag', 10)->nullable()->change();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->string('country', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->char('country', 2)->change();
            $table->char('flag', 2)->nullable()->change();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->char('country', 2)->nullable()->change();
        });
    }
};
