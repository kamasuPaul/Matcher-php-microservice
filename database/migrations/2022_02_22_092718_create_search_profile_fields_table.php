<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchProfileFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_profile_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('min_value')->nullable();
            $table->string('max_value')->nullable();
            $table->enum('value_type', ['range', 'direct']);
            $table->string('search_profile_id');
            $table->foreign('search_profile_id')->references('id')->on('search_profiles');
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
        Schema::dropIfExists('search_profile_fields');
    }
}
