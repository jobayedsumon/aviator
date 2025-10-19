<?php

use App\Models\Setting;
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
        Schema::table('settings', function (Blueprint $table) {
            Setting::insert([
                [
                    'category' => 'deposit_numbers',
                    'value'    => '',
                    'status'   => 1,
                ],
                [
                    'category' => 'helpline_number',
                    'value'    => '',
                    'status'   => 1,
                ]
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            Setting::whereIn('category', ['deposit_numbers', 'helpline_number'])->delete();
        });
    }
};
