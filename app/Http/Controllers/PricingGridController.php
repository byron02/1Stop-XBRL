<?php

namespace App\Http\Controllers;

use App\Models\PricingGridInfo;
use App\Models\PricingGrid;
use App\Models\PricingGrid2;
use App\Models\Taxonomy;
use App\Models\TaxonomyGroup;
use App\Models\Turnaround;
use App\Models\WorkType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use DB;

class PricingGridController extends FrontsiteController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($grid = 0)
    {

        $grids = PricingGridInfo::all();

        $pricingMapA = $this->getPricingTable($grid);

        $worktypes = WorkType::all();
        $taxonomyGroups = Taxonomy::groupBy('name')->get();
        $turnarounds = Turnaround::orderBy('id','desc')->get();

        $types = array();

        $typeA = array();
        $typeA['id'] = 1;
        $typeA['name'] = 'Pricing Grid A';

        $typeB = array();
        $typeB['id'] = 2;
        $typeB['name'] = 'Pricing Grid B';

        $types[1] = $typeA;
        $types[2] = $typeB;



        return view('pricinggrid')
            ->with('pricingMapA', $pricingMapA)
            ->with('worktypes', $worktypes)
            ->with('taxonomyGroups', $taxonomyGroups)
            ->with('turnarounds', $turnarounds)
            ->with('grid_info',$grids)
            ->with('active_grid',$grid)
            ->with('types', $types);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateSaveRequest($request->all())->validate();

        $type = $request['type'];

        // if ($type == 1) {
            $pricingGrid = new PricingGrid();
        // } else {
        //     $pricingGrid = new PricingGrid2();
        // }

        $pricingGrid->floor_page_count = $request['floor_page_count'];
        $pricingGrid->ceiling_page_count = $request['ceiling_page_count'];
        $pricingGrid->turnaround_time = $request['turnaround_time'];
        $pricingGrid->work_type = $request['work_type'];
        $pricingGrid->taxonomy_group = $request['taxonomy_group'];
        $pricingGrid->price = $request['price'];
        $pricingGrid->pricing_info_id = $request['type'];

        $success = $pricingGrid->save();

        if ($success) {
            return redirect('/pricing-grid')
                ->with('success', 'Successfully added price')
                ->with('type',$request['type']);
        } else {
            return redirect('/pricing-grid')
                ->with('failure', 'Failed to add price');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validateSaveRequest($request->all())->validate();

       
        $pricing =  PricingGrid::find($request['price_id']);

        $pricing->floor_page_count = $request['floor_page_count'];
        $pricing->ceiling_page_count = $request['ceiling_page_count'];
        $pricing->turnaround_time = $request['turnaround_time'];
        $pricing->work_type = $request['work_type'];
        $pricing->taxonomy_group = $request['taxonomy_group'];
        $pricing->price = $request['price'];
        $pricing->pricing_info_id = $request['type'];

        $success = $pricing->save();

        if ($success) {
            return redirect('/pricing-grid')
                ->with('success', 'Successfully update price')
                ->with('type',$request['type']);
        } else {
            return redirect('/pricing-grid')
                ->with('failure', 'Failed to update price');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pricing = PricingGrid::find($id);
        $pricing->delete();
    }

    protected function getPricingGrid($gridType) {

        if ($gridType == 1) {
            $pricingGrid = PricingGrid::all();
        } else {
            $pricingGrid = PricingGrid2::all();
        }

        $worktypes = WorkType::all();
        $taxonomyGroups = TaxonomyGroup::all();
        $turnarounds = Turnaround::all();

        $pricingMap = array();

        foreach ($pricingGrid as $pricing) {

            $pricingMapItem = array();
            $pricingMapItem['id'] = $pricing->idpricing_grid;
            $pricingMapItem['floor_page_count'] = $pricing->floor_page_count;
            $pricingMapItem['ceiling_page_count'] = $pricing->ceiling_page_count;
            $pricingMapItem['price'] = $pricing->price;

            foreach($turnarounds as $turnaround) {

                if ($turnaround->id == $pricing->turnaround_time) {
                    $pricingMapItem['turnaround_time'] = $turnaround->name;
                    $pricingMapItem['turnaround_time_id'] = $turnaround->id;
                    break;
                }
            }

            foreach ($worktypes as $workType) {

                if ($workType->id == $pricing->work_type) {
                    $pricingMapItem['work_type'] = $workType->name;
                    $pricingMapItem['work_type_id'] = $workType->id;
                    break;
                }
            }

            foreach ($taxonomyGroups as $taxonomyGroup) {

                if ($taxonomyGroup->id == $pricing->taxonomy_group) {
                    $pricingMapItem['taxonomy_group'] = $taxonomyGroup->group;
                    $pricingMapItem['taxonomy_group_id'] = $taxonomyGroup->id;
                    break;
                }
            }

            $pricingMap[$pricing->idpricing_grid] = $pricingMapItem;
        }

        return $pricingMap;
    }

    protected function validateSaveRequest(array $data) {
        return Validator::make($data, [
            'floor_page_count' => 'required|integer|min:1',
            'ceiling_page_count' => 'required|integer|min:1',
            'price' => 'required|integer|min:1'
        ]);
    }

    public function addPricingInfo(Request $request)
    {
        $grid = PricingGridInfo::all()->last();

        $info = new PricingGridInfo();
        $info->id = $grid->id + 1;
        $info->name = $request->input('grid');
        $info->save();
       echo $info->toJson();
    }

    public function getPricingTable($info,$source = '')
    {
        $limit = request('limit') != null ? request('limit') : 10;
       
        $grid = PricingGrid::select(DB::raw('pricing_grid.*,work_types.name work_name,taxonomy.name group_name,turnaround.name turnaround_name'))
                ->leftJoin('work_types','pricing_grid.work_type','=','work_types.id')
                ->leftJoin('taxonomy','pricing_grid.taxonomy_group','=','taxonomy.id')
                ->leftJoin('turnaround','pricing_grid.turnaround_time','=','turnaround.id')
                ->where('pricing_info_id','=',$info)
                ->orderBy('pricing_grid.taxonomy_group','ASC')
                ->orderBy('pricing_grid.floor_page_count','ASC');
        $grid = $grid->paginate($limit);    
        $grid->appends(Input::except('page'));  

        if($source == '')
        {
            return $grid;
        }
        else
        {
            echo $grid->toJson();
        }


    }

    public function loadPricingGridInfo($priceId)
    {
        $grid = PricingGrid::find($priceId);
        echo $grid->toJson();
    }

}
