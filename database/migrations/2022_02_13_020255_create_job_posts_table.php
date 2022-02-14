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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('location')->nullable();
            $table->string('job_type');
            $table->string('company');
            $table->string('body');
            $table->float('salary_max')->default(0);
            $table->float('salary_min')->default(0);
            $table->string('salary_currency')->nullable();
            $table->string('salary_unit')->nullable();
            $table->string('source_name');
            $table->string('source_url');
            $table->string('apply_url');
            $table->dateTime('source_created_at');
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
        Schema::dropIfExists('job_posts');
    }
};
