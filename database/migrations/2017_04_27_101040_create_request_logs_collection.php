<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mongodb')->createCollection('request_logs', [
            'capped' => true,
            'size' => 52428800,
            'max' => 50000
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection('mongodb')->hasCollection('request_logs')) {
            Schema::connection('mongodb')->drop('request_logs');
        }
    }
}
