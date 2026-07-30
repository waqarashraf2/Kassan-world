<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('website_settings')->updateOrInsert(
            ['key' => 'site_email'],
            [
                'value' => 'kisanworld.magazine@gmail.com',
                'type' => 'email',
                'group' => 'contact',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('website_settings')
            ->where('key', 'site_email')
            ->update(['value' => 'info@kisanworld.pk', 'updated_at' => now()]);
    }
};
