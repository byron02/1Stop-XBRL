<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTaxonomyTableRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('taxonomy')->where('id', 1)->delete();
        DB::table('taxonomy')->where('id', 3)->delete();

        DB::table('taxonomy')->insert(
            array(
                'id' => 6,
                'name' => 'HMRC',
                'group' => 0
            )
        );

        DB::table('taxonomy')->insert(
            array(
                'id' => 7,
                'name' => 'Irish Revenue',
                'group' => 0
            )
        );

        DB::table('taxonomy')->insert(
            array(
                'id' => 8,
                'name' => 'FRS 101',
                'group' => 0
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
        DB::table('taxonomy')->insert(
            array(
                'id' => 1,
                'name' => 'UK-GAAP',
                'group' => 0
            )
        );

        DB::table('taxonomy')->insert(
            array(
                'id' => 3,
                'name' => 'Irish GAAP',
                'group' => 0
            )
        );

        DB::table('taxonomy')->where('id', 6)->delete();
        DB::table('taxonomy')->where('id', 7)->delete();
        DB::table('taxonomy')->where('id', 8)->delete();
    }
}
