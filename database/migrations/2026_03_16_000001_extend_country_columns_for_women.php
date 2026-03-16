<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE competitions ALTER COLUMN country TYPE varchar(10)');
        DB::statement('ALTER TABLE competitions ALTER COLUMN flag TYPE varchar(10)');
        DB::statement('ALTER TABLE teams ALTER COLUMN country TYPE varchar(10)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE competitions ALTER COLUMN country TYPE char(2)');
        DB::statement('ALTER TABLE competitions ALTER COLUMN flag TYPE char(2)');
        DB::statement('ALTER TABLE teams ALTER COLUMN country TYPE char(2)');
    }
};
