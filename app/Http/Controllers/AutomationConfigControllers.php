<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AutomationConfig;

class AutomationConfigControllers extends FrontsiteController
{
    public function index()
    {
    	$config = AutomationConfig::all();
    	return view('layouts.configuration')->with('config',$config);
    }

    public function configSetup($action,$config)
    {
    	$deleted = $action == 'deleted' ? 1 : 0;
    	$active = $deleted == 1 ? 0 : 1;
    	$config = AutomationConfig::where('id','=',$config)->update(['is_deleted' => $deleted,'is_active' => $active]);
    }
}
