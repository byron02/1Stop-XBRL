<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\Country;
use App\Models\User;
use App\Models\PricingGridInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use DB;
class CompaniesController extends FrontsiteController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::select('*');

        if(request('order_by'))
        {
            $companies = $companies->orderBy(request('order_by'),request('sort'));
        }
        else
        {
            $companies = $companies->orderBy('id','desc');
        }
        $companies = $companies->where('active','!=',0);
        $companies = $companies->paginate(15);
        $companyMap = $this->transformToCompanyMap($companies);

        return view('companies')->with('companyMap', $companyMap)
            ->with('companies', $companies);
    }

    protected function validateFilterRequest(array $data) {
        return Validator::make($data, [
            'filter_by' => 'required|string|max:255',
            'query' => 'required|string|max:255'
        ]);
    }

    public function updateAutoInvoice(Request $request) {

        $companyId = $request['company_id'];
        $autoInvoice = $request['auto_invoice'];

        $company = Company::where('id', $companyId)->first();
        $company->autosend_invoice = $autoInvoice;
        $company->save();

        $responseArray = ['status' => '200'];
        return Response::json($responseArray);
    }

    public function udpateAssignInvoiceToProjectName(Request $request) {

        $companyId = $request['company_id_assign_invoice_to_project_name'];
        $assignInvoiceToProjectName = $request['assign_invoice_to_project_name'];

        $company = Company::where('id', $companyId)->first();

        $company->assign_invoice_to_project_name = $assignInvoiceToProjectName;
        $company->save();

        $responseArray = ['status' => '200'];
        return Response::json($responseArray);
    }

    public function filter(Request $request) { 

        $this->validateFilterRequest($request->all())->validate();

        $filterBy = $request["filter_by"];
        $query = $request["query"];

        if ($filterBy == "country") {
            
            $countries = Country::where('name', 'LIKE', '%'.$query.'%')->get();
            
            $countriesArray = [];

            foreach ($countries as $country) {
                array_push($countriesArray, $country->id);
            }

            $companies = Company::whereIn($filterBy, $countriesArray);

        } else if ($filterBy == "payment_method") {

            $paymentMethods = PaymentMethod::where('name', 'LIKE', '%'.$query.'%')->get();
            
            $paymethodMethodsArray = [];

            foreach ($paymentMethods as $paymentMethod) {
                array_push($paymethodMethodsArray, $paymentMethod->id);
            }

            $companies = Company::whereIn($filterBy, $paymethodMethodsArray);

        } else {

            $companies = Company::where($filterBy, 'LIKE', '%'.$query.'%');

        }
        $companies = $companies->where('active','!=',0);
        $companies = $companies->paginate(50);
        $companies->appends([
            'filter_by' => $filterBy,
            'query' => $query
        ]);

        $companyMap = $this->transformToCompanyMap($companies);

        return view('companies')
            ->with('companyMap', $companyMap)
            ->with('companies', $companies)
            ->with('filterBy', $filterBy)
            ->with('query', $query);
    }

    public function transformToCompanyMap($companies) {

        $companyMap = array();

        foreach($companies as $company) {

            $country = Country::where('id', $company->country)->first();
            $paymentMethod = PaymentMethod::where('id', $company->payment_method)->first();

            $companyMapItem = array();
            $companyMapItem["id"] = $company->id;
            $companyMapItem["name"] = $company->name;
            $companyMapItem["email"] = $company->email; 
            $companyMapItem["phone"] = $company->phone; 
            $companyMapItem["address"] = $company->address1.", ".$company->city.", ".$country->name; 

            if (!is_null($paymentMethod) && !empty($paymentMethod)) {
                $companyMapItem["payment_method"] = $paymentMethod->name;   
            } else {
                $companyMapItem["payment_method"] = '';
            }

            $companyMapItem["date_registered"] = $company->date_added;
            
            $pricingGrids = PricingGridInfo::all();
            foreach($pricingGrids as $pricingGrid) {
                if ($pricingGrid->id == $company->pricing_grid) {
                    $companyMapItem["pricing_grid"] = $pricingGrid->name;
                }    
            }

            $companyMapItem["autosend_invoice"] = $company->autosend_invoice;
            $companyMapItem["assign_invoice_to_project_name"] = $company->assign_invoice_to_project_name;

            $companyMap[$company->id] = $companyMapItem;
        } 

        return $companyMap;
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
        //
        $company = new Company;

        $company->name = $request->name;

        $company->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Companyy  $companyy
     * @return \Illuminate\Http\Response
     */
    public function show(Company $companyy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Companyy  $companyy
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {

        $companyId = $request['id'];

        $company = Company::where('id', $companyId)->first();
        $countries = Country::all();
        $paymentMethods = PaymentMethod::all();
        $vendors = User::where('role_id', 4)->get();
        $pricingGrids = PricingGridInfo::all();

        return view('editcompany')->with('company', $company)
            ->with('countries', $countries)
            ->with('paymentMethods', $paymentMethods)
            ->with('vendors', $vendors) 
            ->with('pricingGrids', $pricingGrids);
    }

    protected function validateUdpateRequest(array $data) {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postcode' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'pricing_reference' => 'required|string|max:8'
        ]);

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Companyy  $companyy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        
        // $this->validateUdpateRequest($request->all())->validate();

        $company = Company::where('id', $request['id'])->first();
    
        $this->setObjectProperty('name', $request, $company);
        $this->setObjectProperty('address1', $request, $company);
        $this->setObjectProperty('address2', $request, $company);
        $this->setObjectProperty('address3', $request, $company);
        $this->setObjectProperty('city', $request, $company);
        $this->setObjectProperty('postcode', $request, $company);
        $this->setObjectProperty('country', $request, $company);
        $this->setObjectProperty('phone', $request, $company);
        $this->setObjectProperty('fax', $request, $company);
        $this->setObjectProperty('email', $request, $company);
        $this->setObjectProperty('payment_method', $request, $company);
        $this->setObjectProperty('pricing_reference', $request, $company);
        $this->setObjectProperty('pricing_grid', $request, $company);

        $active = $request['active'];

        if($active) {
            $company->active = 1;
        } else{
            $company->active = 0;
        }

        $defaultVendor = $request['default_vendor'];
        if (is_null($defaultVendor)) {
            $company->default_vendor = 0;
        } else {
            $company->default_vendor = $defaultVendor;
        }

        $discountRate = $request['discount_rate'];
        
        if ($discountRate == -1) {
            $priceAdjustmentRate = $request['price_adjustment_rate'];

            if (!is_null($priceAdjustmentRate)) {
                $company->discount_rate = $priceAdjustmentRate;
            } else {
                $company->discount_rate = 0;
            }
        } else {
            $company->discount_rate = $discountRate;
        }

        $this->setObjectProperty('adjustment_type', $request, $company);

        $successful = $company->save();

        if ($successful) {
            return back()
                ->with('message', 'successfully update company!')
                ->with('company', $company)
                ->withInput();
        } else {
            return back()
                ->with('fail_message', 'failed to update job')
                ->with('company', $company)
                ->withInput();
        }
    }

    function setObjectProperty($key, $defaultRequest, $defaultObject) {

        if (!is_null($defaultRequest[$key])) {
            $defaultObject->$key = $defaultRequest[$key];
        } else {
            $defaultObject->$key = '';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Companyy  $companyy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $companyy)
    {
        //
    }
    function deactivateCompany(Request $request)
    {
        DB::table('companies')->where('id',$request->input('company_id'))->update(['active' => $request->input('status')]);
        $status = $request->input('status') == 0 ? 3 : 1;
        DB::table('users')->where('company_id',$request->input('company_id'))->update(['status' => $status]);
    }

    public function disabledCompanies()
    {
        $companies = Company::select('*');

        if(request('order_by'))
        {
            $companies = $companies->orderBy(request('order_by'),request('sort'));
        }
        else
        {
            $companies = $companies->orderBy('id','desc');
        }
        $companies = $companies->where('active','=',0);
        $companies = $companies->paginate(15);

        return view('layouts.deactivated_companies')->with('companies', $companies);
    }
}
