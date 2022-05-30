<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('cust_id');
            $table->string('cust_name');
            $table->bigInteger('cust_mobile');
            $table->string('whatsapp_no');
            $table->string('cust_email');
            $table->string('session_token')->nullable();
            $table->timestamp('session_created_at')->nullable();
            $table->timestamp('session_expire_at')->nullable();
            $table->timestamp('insert_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
