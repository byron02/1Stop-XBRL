<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDataToTaxAuthority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('tax_authority')->insert(
            array(
                'id' => 1,
                'description' => 'HMRC'
            )
         );

         DB::table('tax_authority')->insert(
            array(
                'id' => 2,
                'description' => 'Irish Revenue'
            )
         );

         DB::table('tax_authority')->insert(
            array(
                'id' => 0,
                'description' => 'None'
            )
         );
         
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('tax_authority')->truncate();
    }
}
