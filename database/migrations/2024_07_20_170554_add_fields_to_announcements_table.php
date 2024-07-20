<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->text('extracted_text')->nullable();
            $table->text('aiquery')->nullable();
            $table->string('subject')->nullable();
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['extracted_text', 'aiquery', 'subject']);
        });
    }

};