<?php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained();
            $table->integer('total');
            $table->string('color')->nullable();
            $table->boolean('sticky');
            $table->string('logo_path')->nullable();
            $table->integer('discount');
            $table->boolean('paid');
            $table->string('email');
            $table->string('checkout_session');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
