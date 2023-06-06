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
        DB::unprepared('CREATE TRIGGER add_user_role AFTER INSERT ON `users` FOR EACH ROW
        BEGIN
           INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (2, "App\\\\Models\\\\\User" ,NEW.id);
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `add_user_role`');
    }
};
