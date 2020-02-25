<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class InsertDataToPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $pricingA = array(33,33,33,33,33,37,42,46,51,55,59,64,68,73,77,80,84,85,85,90,90,91,94,97,100,103,106,106,106,108,110,112,114,116,118,120,122,124,126,128,130,132,134,136,138,140,142,144,146,148);

        for($index = 0; $index < sizeof($pricingA); $index++) {
            $pricingA[$index] = $pricingA[$index] * 1.05;

            DB::table('pricing')->insert(
                array(
                    'pages' => $index + 1,
                    'price' => round($pricingA[$index]),
                    'type' => 0,
                    'created_at' => date_format(Carbon::now(),'Y-m-d H:i:s')
                )
             );

        }

        for($index = sizeof($pricingA); $index < 61; $index++) {
            $pricingA[$index] = (((int)$pricingA[$index -1]) - ((int)$pricingA[$index -2])) + ((int)$pricingA[$index -1]);

            DB::table('pricing')->insert(
                array(
                    'pages' => $index + 1,
                    'price' => (int)($pricingA[$index]),
                    'type' => 0,
                    'created_at' => date_format(Carbon::now(),'Y-m-d H:i:s')
                )
             );
        }

        for($ctr = 1; $ctr <= 14; $ctr++) {
            DB::table('pricing')->insert(
                array(
                    'pages' => $ctr,
                    'price' => 40,
                    'type' => 1,
                    'created_at' => date_format(Carbon::now(),'Y-m-d H:i:s')
                )
             );
        }

        for($ctr = 15; $ctr <= 29; $ctr++) {
            DB::table('pricing')->insert(
                array(
                    'pages' => $ctr,
                    'price' => 60,
                    'type' => 1,
                    'created_at' => date_format(Carbon::now(),'Y-m-d H:i:s')
                )
             );
        }

        for($ctr = 30; $ctr <= 56; $ctr++) {
            DB::table('pricing')->insert(
                array(
                    'pages' => $ctr,
                    'price' => 80,
                    'type' => 1,
                    'created_at' => date_format(Carbon::now(),'Y-m-d H:i:s')
                )
             );
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pricing')->truncate();
    }
}
