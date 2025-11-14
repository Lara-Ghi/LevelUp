<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id')->unique();
            $table->unsignedBigInteger('desk_id')->nullable();
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('username', 60)->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('password');
            $table->unsignedSmallInteger('sitting_position')->nullable();
            $table->unsignedSmallInteger('standing_position')->nullable();
            $table->unsignedSmallInteger('total_points')->default(0);
            $table->unsignedSmallInteger('daily_points')->default(0);
            $table->date('last_points_date')->nullable();
            $table->timestamp('last_daily_reset')->nullable();

            $table->timestamps();
        });

        // Add foreign key if desks table exists
        if (Schema::hasTable('desks')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('desk_id')
                    ->references('id')
                    ->on('desks')
                    ->onDelete('set null');

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};