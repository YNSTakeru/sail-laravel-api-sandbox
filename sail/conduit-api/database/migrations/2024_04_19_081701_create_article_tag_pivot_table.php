<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_tag_pivot', function (Blueprint $table) {
            $table->foreignId("article_id")->constrained("articles")->onDeleteCascade();
            $table->string("tag_name");
            $table->foreign("tag_name")->references("name")->on("tags")->onDeleteCascade();
            $table->primary(["article_id", "tag_name"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tag_pivot');
    }
};
