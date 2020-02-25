<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDataToPricingGridInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('pricing_grid_info');

        Schema::create('pricing_grid_info', function(Blueprint $table) {
			$table->integer('id');
            $table->string('name');
        });
        
        DB::table('pricing_grid_info')->insert(
            array(
                'id' => 0,
                'name' => 'Pricing Grid A'
            )
         );

         DB::table('pricing_grid_info')->insert(
            array(
                'id' => 1,
                'name' => 'Pricing Grid B'
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
        Schema::create('pricing_grid_info', function(Blueprint $table) {
			$table->increments('id');
            $table->string('name');
        }); 
    }
}
