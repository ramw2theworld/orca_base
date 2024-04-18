<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesAndContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->timestamps();
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        Schema::create('content_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_key_id');
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('content_key_id')->references('id')->on('content_keys');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('contents');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('content_keys');
    }
}
