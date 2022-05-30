<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtekUserData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atek_user_data', function (Blueprint $table) {
            $table->id('atek_user_id');
            $table->integer('cust_id');
            $table->string('cust_name');
            $table->bigInteger('cust_mobile');
            $table->string('whatsapp_no');
            $table->string('cust_email');
            $table->string('atek_token')->nullable();
            $table->timestamp('token_created_at')->nullable();
            $table->timestamp('token_expire_at')->nullable();
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
        //
    }
}
