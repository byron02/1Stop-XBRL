<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameClientsDepartmentsEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients_departments', function (Blueprint $table) {
            //

//            $table->renameColumn('email_address', 'email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_departments', function (Blueprint $table) {
            //
//            $table->renameColumn('email', 'email_address');
        });
    }
}
