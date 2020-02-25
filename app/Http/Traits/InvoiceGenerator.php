<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Models\JobStatus;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Pricing;
use App\Models\Turnaround;
use App\Models\JobsSourceFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPExcel_Worksheet_Drawing;
use DB;
use PDF;

trait InvoiceGenerator
{

    protected function validateGenerateInvoiceRequest(array $data)
    {

        return Validator::make($data, [
            "job-generate-invoice" => 'required|string|distinct|min:1'
        ]);
    }

    public function generateInvoice(Request $request)
    {

        $validator = $this->validateGenerateInvoiceRequest($request->all())->validate();

        $targetFullStoragePath = storage_path("invoices/xls");
        // $name = Carbon::now()->toDateTimeString();
        $jobNumbers = $request['job-generate-invoice'];
        $jobNumbers = json_decode($jobNumbers, true);
        $name = time();
        $latestInvoice = Invoice::orderBy('id', 'desc')->first();
        $latestInvoiceNumber = $latestInvoice->invoice_number;
        $latestInvoiceNumber = explode('-', $latestInvoiceNumber);
        $latestInvoiceNumber = (int) $latestInvoiceNumber[0];
        $latestInvoiceNumber = $latestInvoiceNumber + 1;
        $newInvoiceNumber = $latestInvoiceNumber . '-' . date('dmY') . '-' . $jobNumbers[0];
        $newInvoiceNumber = isset($request['invoice_number']) ? $request['invoice_number'] : $newInvoiceNumber;


        $company = Company::where('id', $request['company-generate-invoice'])->first();
        $jobs = DB::table('jobs_source_files AS jsf')
            ->select(DB::raw('jb.*,jsf.page_count,tr.number_of_days,jsf.tax_computed,jb.work_type,tm.name taxonomy_name'))
            ->whereIn('jsf.job_id', $jobNumbers)
            ->where('jsf.type', '=', '0')
            ->where('jsf.is_removed', '0')
            ->where('jsf.page_count', '!=', '0')
            ->where('jb.status', '!=', '9')
            ->leftJoin('jobs AS jb', 'jb.id', '=', 'jsf.job_id')
            ->leftJoin('turnaround AS tr', 'tr.id', '=', 'jb.turnaround')
            ->leftJoin('taxonomy AS tm', 'tm.id', '=', 'jb.taxonomy')

            ->get();


        Excel::create($name, function ($excel) use ($request, $newInvoiceNumber, $jobs, $company) {

            $excel->sheet('Sheetname', function ($sheet) use ($request, $newInvoiceNumber, $jobs, $company) {

                $user = User::where('company_id', $company->id)->first();
                $country = Country::where('id', $company->country)->first();

                $date = Carbon::now();
                $date = date_format($date, 'd M Y');

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  9,
                    )
                ));

                $sheet->setAutoSize(false);

                $sheet->setWidth(array(
                    'A'     =>  35,
                    'B'     =>  15,
                    'C'     =>  15,
                    'D'     =>  15,
                    'E'     =>  20,
                    'F'     =>  20,
                ));

                $invoiceLogo = new PHPExcel_Worksheet_Drawing;
                $invoiceLogo->setPath(public_path("img/xbrl_logo_2.png"));
                $invoiceLogo->setCoordinates('A2');
                $invoiceLogo->setWorksheet($sheet);

                $sheet->setSize(array(
                    'A2' => array(
                        'height'    => 45
                    )
                ));

                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('TAX INVOICE');
                    $cell->setFontSize(24);
                });

                $sheet->cell('A6',  function ($cell) {
                    $cell->setValue('Invoice Date');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B6', '1STOPXBRL');

                if (count($jobs) == 1 && $company->assign_invoice_to_project_name) {
                    $sheet->cell('A14', $jobs[0]->project_name);
                } else {
                    $sheet->cell('A14', $company->name);
                }

                $sheet->cell('A7', $date);
                $sheet->cell('B7', '1STOPXBRL LIMITED');

                $sheet->cell('A15', $company->address1);

                $sheet->cell('A8',  function ($cell) {
                    $cell->setValue('Account Number');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B8',  '601 International House');

                // $sheet->cell('A8',  $company->address1);
                $sheet->cell('A9',  $user->id);
                $sheet->cell('B9',  '223 Regent Street');

                $sheet->cell('A17',  $company->address2);
                $sheet->cell('A18',  " " . $company->postcode);
                $sheet->cell('A10',  function ($cell) {
                    $cell->setValue('Invoice Number');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B10',  'London');

                $sheet->cell('A16',  $company->city);
                $sheet->cell('A11', $newInvoiceNumber);
                $sheet->cell('B11', 'W1B 2QD');

                $sheet->cell('A12',  function ($cell) {
                    $cell->setValue('VAT Number');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B12',  'UNITED KINGDOM');

                $sheet->cell('A17',  $country->name);
                $sheet->cell('A13', ''); // Vat Number Here

                $sheet->getStyle('A6:A13')->getAlignment()->applyFromArray(
                    array('horizontal' => 'left')
                );

                $sheet->cells('A20:F20', function ($cells) {
                    $cells->setBorder('thin', 'thin', 'medium', 'thin');
                });



                $sheet->cell('A20',  function ($cell) {
                    $cell->setValue('Description of Service Supplied');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B20',  function ($cell) {
                    $cell->setValue('Pages');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C20',  function ($cell) {
                    $cell->setValue('Type');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('D20',  function ($cell) {
                    $cell->setValue('Client PO');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E20',  function ($cell) {
                    $cell->setValue('Sub Total');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F20',  function ($cell) {
                    $cell->setValue('Total (Inc. Vat)');
                    $cell->setFontWeight('bold');
                });

                $rowNumber = 21;
                $subTotal = 0;
                $priceSub = 0;
                $dueDate = $jobs[0]->due_date;

                foreach ($jobs as $job) {
                    $type = $job->tax_computed == 0 ? 'Statutory Account' : 'Tax Computation';
                    $pricing = $this->getJobPricing($job->page_count, $job->company, $job->turnaround, $type, $job->taxonomy_name);

                    // $price = isset($pricing[0]) ? $pricing[0]->total_price : $job->computed_price;
                    $price = $pricing['total_price'];

                    // if ($dueDate->gt($job->due_date)) {
                    //     $dueDate = $job->due_date;
                    // }

                    $tax_price = $price - $job->tax_computation_price;
                    $sheet->appendRow(array($job->project_name, $job->page_count . ' (' . $job->number_of_days . ' days) ', $type, $job->purchase_order, $tax_price, $price));

                    $sheet->cells('A' . $rowNumber . ':F' . $rowNumber, function ($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $subTotal += $price;
                    $priceSub += $tax_price;
                    $rowNumber++;
                }

                // foreach($jobs as $job) {

                //     // $pricingGridType = $company->pricing_grid;

                //     // $pricing = Pricing::where('pages', $job->total_pages_submitted)
                //     //     ->where('type', $pricingGridType)
                //     //     ->orderBy('created_at', 'DESC')
                //     //     ->first();

                //     $price = $job->computed_price;
                //     // $price = !empty($pricing) ? $pricing->price : 0;


                //     if ($dueDate->gt($job->due_date)) {
                //         $dueDate = $job->due_date;
                //     }

                //     $turnaround = Turnaround::where('id',$job->turnaround)->first();

                //     $tax_price = $price+($price * 0.20);
                //     $sheet->appendRow(array($job->project_name, $job->total_pages_submitted.' ('. $turnaround->number_of_days .' days) ','Type here!', $job->purchase_order, $price, $tax_price));

                //     $sheet->cells('A'.$rowNumber.':F'.$rowNumber, function($cells) {
                //         $cells->setBorder('thin', 'thin', 'thin', 'thin');
                //     });

                //     $subTotal+= $price;
                //     $rowNumber++;
                // }

                $rowNumber = $rowNumber + 1;
                $sheet->cell('E' . $rowNumber, 'Subtotal');
                $sheet->cell('F' . $rowNumber, $subTotal);

                $totalVat = $priceSub - $subTotal;

                $rowNumber++;
                $sheet->cell('E' . $rowNumber, 'Total VAT 20%');
                $sheet->cell('F' . $rowNumber, $totalVat); // Total Vat Here

                // $sheet->cells('E'.$rowNumber.':F'.$rowNumber, function($cells) {
                //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                // });

                $totalGbp = $totalVat + $subTotal;

                $rowNumber++;
                $sheet->cell('E' . $rowNumber, function ($cell) {
                    $cell->setValue('TOTAL GBP');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F' . $rowNumber,  $totalGbp); // Total GBP Here

                $rowNumber++;
                // $sheet->cell('D'.$rowNumber,'Less Amount Paid');
                // $sheet->cell('E'.$rowNumber, ''); // Less Amount Paid Here

                // $sheet->cells('D'.$rowNumber.':E'.$rowNumber, function($cells) {
                //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                // });

                $rowNumber++;
                // $sheet->cell('F'.$rowNumber, function($cell) {
                //     $cell->setValue('AMOUNT DUE');
                //     $cell->setFontWeight('bold');
                // });
                // $sheet->cell('G'.$rowNumber, function($cell) {
                //     $cell->setValue(''); // Amount Due Here
                //     $cell->setFontWeight('bold');
                // }); 

                // $sheet->setColumnFormat(array(
                //     'G20:G'.$rowNumber => '0.00'
                // ));           

                $sheet->getStyle('F20:G' . $rowNumber)->getAlignment()->applyFromArray(
                    array('horizontal' => 'right')
                );
                $rowNumber += 3;

                $sheet->cell('A' . $rowNumber, function ($cell) use ($dueDate) {
                    $cell->setValue('Due Date: ' . date('d M Y', strtotime($dueDate))); // Due Date Here
                    $cell->setFontSize(11);
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E' . $rowNumber, 'Registered in England & Wales No. 8072489');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Bank: Metro Bank');
                $sheet->cell('E' . $rowNumber, 'Vat Number 136 3220 45');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Branch: One Southampton Row, London WC1B 5HA');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Acct Name: 1STOPXBRL LIMITED');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Sort Code: 23-05-80');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Account Number: 11039758');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'IBAN: GB56MYMB23058011039758');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Swift: MYMBGB2L');
                $rowNumber += 3;

                $sheet->cell('A' . $rowNumber, 'Specialists in iXBRL tagging and E-Filing. Recognised by HMRC and Companies House. HMRC Vendor ID1698 - HMRC eFiling');

                $sheet->getStyle('A' . $rowNumber)->getAlignment()->setWrapText(true);
                $rowNumber += 11;

                $sheet->cell('A' . $rowNumber, function ($cell) {
                    $cell->setValue('Company Registration No: 8072489. Registered Office: Attention: 1STOPXBRL LIMITED, 601 International House, 223 Regent Street, London, London, W1B 2QD, United Kingdom.'); // Due Date Here
                    $cell->setFontSize(7);
                });
            });
        })->store('xls', $targetFullStoragePath);

        $gcsFileName = $newInvoiceNumber . '.xls';
        $fileName = $name . '.xls';
        $file = $targetFullStoragePath . '/' . $fileName;
        $fileContents = file_get_contents($file);
        $jobs = Job::whereIn('id', $jobNumbers)->get();
        try {

            $storage = Storage::disk('gcs');
            $storage->put('invoices/' . $gcsFileName, $fileContents);

            foreach ($jobs as $job) {
                $pricingGridType = $company->pricing_grid;

                $pricing = Pricing::where('pages', $job->total_pages_submitted)
                    ->where('type', $pricingGridType)
                    ->orderBy('created_at', 'DESC')
                    ->first();
                $price = !empty($pricing) ? $pricing->price : 0;

                $invoice_data = DB::table('invoices')->whereRaw("job_id = " . $job->id . " AND invoice_number = '" . $newInvoiceNumber . "'")->first();
                if (!empty($invoice_data)) {
                    $arr = array(
                        'rate' => $price,
                        'total' => ($price * 0.2),
                        'date_imported' => date('Y-m-d H:i:s')
                    );

                    DB::table('invoices')->whereRaw("job_id = " . $job->id . " AND invoice_number = '" . $newInvoiceNumber . "'")->update($arr);
                } else {
                    $invoice = new Invoice();
                    $invoice->invoice_number = $newInvoiceNumber;
                    $invoice->job_id = $job->id;
                    $invoice->quantity = 1;
                    $invoice->rate = $price;
                    $invoice->total = $price * 0.2;
                    $invoice->is_imported_to_xero = 0;
                    $invoice->date_imported = date('Y-m-d H:i:s');
                    $invoice->save();

                    $latestInvoice = Invoice::orderBy('id', 'desc')->first();
                    $invoiceId = $latestInvoice->id;
                    $job->invoice = $invoiceId;
                    $job->is_invoiced = 1;
                    $job->save();
                }
            }

            $responseArray = ['filename' => $fileName, 'gcs_filename' => $gcsFileName, 'status' => '200'];
            return $responseArray;
        } catch (\Exception $e) {

            File::delete($file);

            $responseArray = ['error' => 'Failed to generate invoice', 'status' => '200'];
            return $responseArray;
        }
    }

    public function forInvoiceGenerator(Request $request)
    {

        $validator = $this->validateGenerateInvoiceRequest($request->all())->validate();

        $targetFullStoragePath = storage_path("invoices/xls");
        // $name = Carbon::now()->toDateTimeString();
        $jobNumbers = $request['job-generate-invoice'];
        $jobNumbers = json_decode($jobNumbers, true);
        $name = time();
        $latestInvoice = Invoice::orderBy('id', 'desc')->first();
        $latestInvoiceNumber = $latestInvoice->invoice_number;
        $latestInvoiceNumber = explode('-', $latestInvoiceNumber);
        $latestInvoiceNumber = (int) $latestInvoiceNumber[0];
        $latestInvoiceNumber = $latestInvoiceNumber + 1;
        $newInvoiceNumber = $latestInvoiceNumber . '-' . date('dmY') . '-' . $jobNumbers[0];
        $newInvoiceNumber = isset($request['invoice_number']) ? $request['invoice_number'] : $newInvoiceNumber;


        $company = Company::where('id', $request['company-generate-invoice'])->first();
        $jobs = DB::table('jobs_source_files AS jsf')
            ->select(DB::raw('jb.*,jsf.page_count,tr.number_of_days,jb.is_invoiced,jsf.tax_computed,jb.work_type,tm.name taxonomy_name'))
            ->whereIn('jsf.job_id', $jobNumbers)
            ->where('jsf.type', '=', '0')
            ->where('jsf.is_removed', '0')
            ->where('jsf.page_count', '!=', '0')
            ->where('jb.status', '!=', '9')
            ->leftJoin('jobs AS jb', 'jb.id', '=', 'jsf.job_id')
            ->leftJoin('turnaround AS tr', 'tr.id', '=', 'jb.turnaround')
            ->leftJoin('taxonomy AS tm', 'tm.id', '=', 'jb.taxonomy')

            ->get();


        Excel::create($name, function ($excel) use ($request, $newInvoiceNumber, $jobs, $company) {

            $excel->sheet('Sheetname', function ($sheet) use ($request, $newInvoiceNumber, $jobs, $company) {

                $user = User::where('company_id', $company->id)->first();
                $country = Country::where('id', $company->country)->first();

                $date = Carbon::now();
                $date = date_format($date, 'd M Y');

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  9,
                    )
                ));

                $sheet->setAutoSize(false);

                $sheet->setWidth(array(
                    'A'     =>  35,
                    'B'     =>  35,
                    'C'     =>  15,
                    'D'     =>  15,
                    'E'     =>  15,
                    'F'     =>  20,
                    'G'     =>  20,
                ));

                $invoiceLogo = new PHPExcel_Worksheet_Drawing;
                $invoiceLogo->setPath(public_path("img/xbrl_logo_2.png"));
                $invoiceLogo->setCoordinates('A2');
                $invoiceLogo->setWorksheet($sheet);

                $sheet->setSize(array(
                    'A2' => array(
                        'height'    => 45
                    )
                ));

                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('TAX INVOICE');
                    $cell->setFontSize(24);
                });

                $sheet->cell('A6',  function ($cell) {
                    $cell->setValue('Invoice Date');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B6', '1STOPXBRL');

                if (count($jobs) == 1 && $company->assign_invoice_to_project_name) {
                    $sheet->cell('A14', $jobs[0]->project_name);
                } else {
                    $sheet->cell('A14', $company->name);
                }

                $sheet->cell('A7', $date);
                $sheet->cell('B7', '1STOPXBRL LIMITED');

                $sheet->cell('A15', $company->address1);

                $sheet->cell('A8',  function ($cell) {
                    $cell->setValue('Account Number');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B8',  '601 International House');

                // $sheet->cell('A8',  $company->address1);
                $sheet->cell('A9',  $user->id);
                $sheet->cell('B9',  '223 Regent Street');

                $sheet->cell('A17',  $company->address2);
                $sheet->cell('A18',  " " . $company->postcode);
                $sheet->cell('A10',  function ($cell) {
                    $cell->setValue('Invoice Number');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B10',  'London');

                $sheet->cell('A16',  $company->city);
                $sheet->cell('A11', '');
                $sheet->cell('B11', 'W1B 2QD');

                $sheet->cell('A12',  function ($cell) {
                    $cell->setValue('VAT Number');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B12',  'UNITED KINGDOM');

                $sheet->cell('A17',  $country->name);
                $sheet->cell('A13', ''); // Vat Number Here

                $sheet->getStyle('A6:A13')->getAlignment()->applyFromArray(
                    array('horizontal' => 'left')
                );

                $sheet->cells('A20:G20', function ($cells) {
                    $cells->setBorder('thin', 'thin', 'medium', 'thin');
                });



                $sheet->cell('A20',  function ($cell) {
                    $cell->setValue('Job ID');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B20',  function ($cell) {
                    $cell->setValue('Description of Service Supplied');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C20',  function ($cell) {
                    $cell->setValue('Pages');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D20',  function ($cell) {
                    $cell->setValue('Type');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('E20',  function ($cell) {
                    $cell->setValue('Client PO');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F20',  function ($cell) {
                    $cell->setValue('Sub Total');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G20',  function ($cell) {
                    $cell->setValue('Total (Inc. Vat)');
                    $cell->setFontWeight('bold');
                });

                $rowNumber = 21;
                $subTotal = 0;
                $priceSub = 0;
                $dueDate = $jobs[0]->due_date;

                foreach ($jobs as $job) {
                    $type = $job->tax_computed == 0 ? 'Statutory Account' : 'Tax Computation';
                    $pricing = $this->getJobPricing($job->page_count, $job->company, $job->turnaround, $type, $job->taxonomy_name);

                    $price = isset($pricing[0]) ? $pricing[0]->total_price : $job->computed_price;

                    // if ($dueDate->gt($job->due_date)) {
                    //     $dueDate = $job->due_date;
                    // }

                    $tax_price = $price - $job->tax_computation_price;
                    $sheet->appendRow(array($job->id,$job->project_name, $job->page_count . ' (' . $job->number_of_days . ' days) ', $type, $job->purchase_order, $tax_price, $price));

                    $sheet->cells('A' . $rowNumber . ':G' . $rowNumber, function ($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $priceSub += $price;
                    $subTotal += $tax_price;


                    $rowNumber++;
                }

                // foreach($jobs as $job) {

                //     // $pricingGridType = $company->pricing_grid;

                //     // $pricing = Pricing::where('pages', $job->total_pages_submitted)
                //     //     ->where('type', $pricingGridType)
                //     //     ->orderBy('created_at', 'DESC')
                //     //     ->first();

                //     $price = $job->computed_price;
                //     // $price = !empty($pricing) ? $pricing->price : 0;


                //     if ($dueDate->gt($job->due_date)) {
                //         $dueDate = $job->due_date;
                //     }

                //     $turnaround = Turnaround::where('id',$job->turnaround)->first();

                //     $tax_price = $price+($price * 0.20);
                //     $sheet->appendRow(array($job->project_name, $job->total_pages_submitted.' ('. $turnaround->number_of_days .' days) ','Type here!', $job->purchase_order, $price, $tax_price));

                //     $sheet->cells('A'.$rowNumber.':F'.$rowNumber, function($cells) {
                //         $cells->setBorder('thin', 'thin', 'thin', 'thin');
                //     });

                //     $subTotal+= $price;
                //     $rowNumber++;
                // }
                $rowNumber = $rowNumber + 1;


                $sheet->cell('F' . $rowNumber, 'Subtotal');
                $sheet->cell('G' . $rowNumber, $subTotal);

                $totalVat = $priceSub - $subTotal;


                $rowNumber++;
                $sheet->cell('F' . $rowNumber, 'Total VAT 20%');
                $sheet->cell('G' . $rowNumber, $totalVat); // Total Vat Here

                // $sheet->cells('E'.$rowNumber.':F'.$rowNumber, function($cells) {
                //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                // });

                $totalGbp = $totalVat + $subTotal;

                $rowNumber++;
                $sheet->cell('F' . $rowNumber, function ($cell) {
                    $cell->setValue('TOTAL GBP');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G' . $rowNumber, $totalGbp); // Total GBP Here

                $rowNumber++;
                // $sheet->cell('D'.$rowNumber,'Less Amount Paid');
                // $sheet->cell('E'.$rowNumber, ''); // Less Amount Paid Here

                // $sheet->cells('D'.$rowNumber.':E'.$rowNumber, function($cells) {
                //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                // });

                $rowNumber++;
                // $sheet->cell('F'.$rowNumber, function($cell) {
                //     $cell->setValue('AMOUNT DUE');
                //     $cell->setFontWeight('bold');
                // });
                // $sheet->cell('G'.$rowNumber, function($cell) {
                //     $cell->setValue(''); // Amount Due Here
                //     $cell->setFontWeight('bold');
                // }); 

                // $sheet->setColumnFormat(array(
                //     'G20:G'.$rowNumber => '0.00'
                // ));           

                $sheet->getStyle('F20:G' . $rowNumber)->getAlignment()->applyFromArray(
                    array('horizontal' => 'right')
                );
                $rowNumber += 3;

                $sheet->cell('A' . $rowNumber, function ($cell) use ($dueDate) {
                    $cell->setValue('Due Date: ' . date('d M Y', strtotime($dueDate))); // Due Date Here
                    $cell->setFontSize(11);
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E' . $rowNumber, 'Registered in England & Wales No. 8072489');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Bank: Metro Bank');
                $sheet->cell('E' . $rowNumber, 'Vat Number 136 3220 45');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Branch: One Southampton Row, London WC1B 5HA');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Acct Name: 1STOPXBRL LIMITED');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Sort Code: 23-05-80');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Account Number: 11039758');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'IBAN: GB56MYMB23058011039758');
                $rowNumber++;

                $sheet->cell('A' . $rowNumber, 'Swift: MYMBGB2L');
                $rowNumber += 3;

                $sheet->cell('A' . $rowNumber, 'Specialists in iXBRL tagging and E-Filing. Recognised by HMRC and Companies House. HMRC Vendor ID1698 - HMRC eFiling');

                $sheet->getStyle('A' . $rowNumber)->getAlignment()->setWrapText(true);
                $rowNumber += 11;

                $sheet->cell('A' . $rowNumber, function ($cell) {
                    $cell->setValue('Company Registration No: 8072489. Registered Office: Attention: 1STOPXBRL LIMITED, 601 International House, 223 Regent Street, London, London, W1B 2QD, United Kingdom.'); // Due Date Here
                    $cell->setFontSize(7);
                });
            });
        })->store('xls', $targetFullStoragePath);

        $gcsFileName = $newInvoiceNumber . '.xls';
        $fileName = $name . '.xls';
        $file = $targetFullStoragePath . '/' . $fileName;
        $fileContents = file_get_contents($file);
        $jobs = Job::whereIn('id', $jobNumbers)->get();
        try {

            $storage = Storage::disk('gcs');
            $storage->put('invoices/' . $gcsFileName, $fileContents);

            foreach ($jobs as $job) {
                $pricingGridType = $company->pricing_grid;

                $pricing = Pricing::where('pages', $job->total_pages_submitted)
                    ->where('type', $pricingGridType)
                    ->orderBy('created_at', 'DESC')
                    ->first();
                $price = !empty($pricing) ? $pricing->price : 0;

                $invoice = new Invoice();
                $invoice->invoice_number = $newInvoiceNumber;
                $invoice->job_id = $job->id;
                $invoice->quantity = 1;
                $invoice->rate = $price;
                $invoice->total = $job->country == 222 ? $price * 0.2 : 0;
                $invoice->is_imported_to_xero = 0;
                $invoice->date_imported = date('Y-m-d H:i:s');
                $invoice->save();

                $latestInvoice = Invoice::orderBy('id', 'desc')->first();
                $invoiceId = $job->is_invoiced == 0 ? 0 : $latestInvoice->id;
                $job->invoice = $job->is_invoiced == 0 ? 0 : $invoiceId;
                $job->is_invoiced = $job->is_invoiced == 0 ? 0 : 1;
                $job->save();
            }

            $responseArray = ['filename' => $fileName, 'gcs_filename' => $gcsFileName, 'status' => '200'];
            return $responseArray;
        } catch (\Exception $e) {

            File::delete($file);

            $responseArray = ['error' => 'Failed to generate invoice', 'status' => '200'];
            return $responseArray;
        }
    }

    public function invoicedPdf($invoice_number)
    {
        $invoice = DB::table('invoices as inv')
            ->select(
                DB::raw('usr.id,
                                  inv.date_imported,
                                  inv.invoice_number,
                                  cp.name company_name,
                                  cp.assign_invoice_to_project_name,
                                  js.project_name,
                                  cp.address1,
                                  cp.city,
                                  ct.name country,
                                  cp.postcode,
                                  cp.city,
                                  js.purchase_order,
                                  js.due_date')
            )
            ->leftJoin('jobs as js', 'js.id', '=', 'inv.job_id')
            ->leftJoin('companies as cp', 'cp.id', '=', 'js.company')
            ->leftJoin('countries as ct', 'ct.id', '=', 'cp.country')
            ->leftJoin('users as usr', 'usr.company_id', '=', 'cp.id')
            ->where('inv.invoice_number', $invoice_number)
            ->get();

        $jobs = DB::table('jobs_source_files AS jsf')
            ->select(
                DB::raw('jb.*,
                                    jsf.page_count,
                                    tr.number_of_days,
                                    jsf.tax_computed,
                                    jb.work_type,
                                    tm.name taxonomy_name,
                                    cp.discount_rate,
                                    cp.country as country')
            )
            ->where('inv.invoice_number', $invoice_number)
            ->where('jsf.type', '=', '0')
            ->where('jsf.is_removed', '0')
            ->leftJoin('jobs AS jb', 'jb.id', '=', 'jsf.job_id')
            ->leftJoin('turnaround AS tr', 'tr.id', '=', 'jb.turnaround')
            ->leftJoin('taxonomy AS tm', 'tm.id', '=', 'jb.taxonomy')
            ->leftJoin('invoices as inv', 'inv.job_id', '=', 'jb.id')
            ->leftJoin('companies as cp', 'cp.id', '=', 'jb.company')
            ->get();


        if (!empty($invoice) && !is_null($invoice)) {
            $info = [];
            $subtotal = 0;
            $vat = 0;
            $gross = 0;
            $tax = 0;
            
            foreach ($jobs as $k => $job) {

                if ($job->country == 222) {
                    $tax = 0.20;
                }
                $type = $job->tax_computed == 0 ? 'Statutory Account' : 'Tax Computation';
                $pricing = $this->getJobPricing($job->page_count, $job->company, $job->turnaround, $type, $job->taxonomy_name);
                // $price = isset($pricing[0]) ? $pricing[0]->total_price : 0;
                $price = $pricing['total_price'];
                $job->page_count . '~' . $job->company . '~' . $job->turnaround . '~' . $type . '~' . $job->taxonomy_name . '<br/>';
                $info[$k]['project_name'] = $job->project_name;
                $info[$k]['page_count'] = $job->page_count;
                $info[$k]['number_of_days'] = $job->number_of_days;
                $info[$k]['type'] = $type;
                $info[$k]['purchase_order'] = $job->purchase_order;
                $info[$k]['price'] = number_format($price, 2);
                $info[$k]['total_gbp'] = number_format($price + ($price * $tax), 2);

                $subtotal += $price;
                $vat += ($price * $tax);
                $gross +=  $price + ($price * $tax);
            }
            $summary = [];
            $summary['subtotal'] = $subtotal;
            $summary['vat'] = $vat;
            $summary['gross'] = $gross;

            $arr = array('invoice' => $invoice->toArray(), 'job' => $info, 'summary' => $summary);
            $pdf = PDF::loadView('pdf', $arr)->setPaper('L', 'landscape');
            return $pdf;
        }
    }

    public function getJobPricing($page, $company, $turnaround, $work_type, $taxonomy)
    {
        // $price = DB::table('companies AS cp')
        //             // ->select(DB::raw('cp.id,cp.name,
        //             //             pg.price,(pg.price * (cp.discount_rate * 0.01 )) discount,
        //             //             (pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price,
        //             //             pgi.name pricing_info')
        //             //          )
        //               ->select(DB::raw('(pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price'))
        //             ->leftJoin('pricing_grid_info AS pgi','pgi.id','=','cp.pricing_grid')
        //             ->leftJoin('pricing_grid AS pg','pg.pricing_info_id','=','pgi.id')
        //             ->leftJoin('taxonomy as tm','tm.id','=','pg.taxonomy_group')
        //              ->whereRaw('cp.id = '.$company.'
        //                             AND
        //                                 ('.$page.' BETWEEN pg.floor_page_count AND pg.ceiling_page_count)
        //                             AND
        //                                 pg.turnaround_time = '.$turnaround.'
        //                             AND
        //                                 tm.name = "'.$taxonomy.'"
        //                             AND
        //                                 pg.work_type = '."(SELECT id FROM work_types WHERE name = '".$work_type."')")
        //             ->get();
        $excess_page  = 0;
        if ($page > 50) {
            $excess_page = $page - 50;
            $page = 50;
        }
        $taxonomy = str_replace('~', '/', $taxonomy);
        $price = DB::table('companies AS cp')
            // ->select(DB::raw('cp.id,cp.name,
            //             pg.price,(pg.price * (cp.discount_rate * 0.01 )) discount,
            //             (pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price,
            //             pgi.name pricing_info')
            //          )
            ->select(DB::raw('IF(cp.adjustment_type = 0,(pg.price - (pg.price * (cp.discount_rate * 0.01 ))),(pg.price + (pg.price * (cp.discount_rate * 0.01 )))) total_price,pgi.id pricing_info_id'))
            ->leftJoin('pricing_grid_info AS pgi', 'pgi.id', '=', 'cp.pricing_grid')
            ->leftJoin('pricing_grid AS pg', 'pg.pricing_info_id', '=', 'pgi.id')
            ->leftJoin('taxonomy as tm', 'tm.id', '=', 'pg.taxonomy_group')
            ->whereRaw('cp.id = ' . $company . '
                                    AND
                                        (' . $page . ' BETWEEN pg.floor_page_count AND pg.ceiling_page_count)
                                    AND
                                        pg.turnaround_time = ' . $turnaround . '
                                    AND
                                        tm.name = "' . $taxonomy . '"
                                    AND
                                        pg.work_type = ' . "(SELECT id FROM work_types WHERE name = '" . $work_type . "')")
            ->get();
            
        $arr = [];
        if (!empty($price) && isset($price[0])) {
            $price = $price[0];
            $add = 0;
            $additional = DB::table('pricing_grid_config')->select('*')->where('pricing_id', $price->pricing_info_id)->get();
            if (!empty($additional) && isset($additional[0])) {
                $add += $excess_page * $additional[0]->price;
            }
            $arr['total_price'] = $price->total_price + $add;
        }
        // echo json_encode($arr,true);

        return $arr;
    }






    public function generateOverwriteInvoice($invoice_id, $job_id, $invoice_number, $company_id)
    {

        $targetFullStoragePath = storage_path("invoices/xls");
        // $name = Carbon::now()->toDateTimeString();
        $jobNumbers = json_encode(array($job_id));
        $jobNumbers = json_decode($jobNumbers, true);
        $name = time();
        $latestInvoice = Invoice::where('id', $invoice_id)->first();
        $latestInvoiceNumber = $latestInvoice->invoice_number;
        $latestInvoiceNumber = explode('-', $latestInvoiceNumber);
        $latestInvoiceNumber = (int) $latestInvoiceNumber[0];
        $latestInvoiceNumber = $latestInvoiceNumber + 1;
        $newInvoiceNumber = $latestInvoiceNumber . '-' . date('dmY') . '-' . $jobNumbers[0];
        $newInvoiceNumber = $invoice_number;


        $company = Company::where('id', $company_id)->first();
        $jobs = DB::table('jobs_source_files AS jsf')
            ->select(DB::raw('jb.*,jsf.page_count,tr.number_of_days,jsf.tax_computed,jb.work_type,tm.name taxonomy_name'))
            ->whereIn('jsf.job_id', $jobNumbers)
            ->where('jsf.type', '=', '0')
            ->where('jsf.is_removed', '0')
            ->where('jsf.page_count', '!=', '0')
            ->where('jb.status', '!=', '9')
            ->leftJoin('jobs AS jb', 'jb.id', '=', 'jsf.job_id')
            ->leftJoin('turnaround AS tr', 'tr.id', '=', 'jb.turnaround')
            ->leftJoin('taxonomy AS tm', 'tm.id', '=', 'jb.taxonomy')
            ->get();

        if (!empty($jobs) && isset($jobs[0])) {
            Excel::create($name, function ($excel) use ($newInvoiceNumber, $jobs, $company) {

                $excel->sheet('Sheetname', function ($sheet) use ($newInvoiceNumber, $jobs, $company) {

                    $user = User::where('company_id', $company->id)->first();
                    $country = Country::where('id', $company->country)->first();

                    $date = Carbon::now();
                    $date = date_format($date, 'd M Y');

                    $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  9,
                        )
                    ));

                    $sheet->setAutoSize(false);

                    $sheet->setWidth(array(
                        'A'     =>  35,
                        'B'     =>  15,
                        'C'     =>  15,
                        'D'     =>  15,
                        'E'     =>  20,
                        'F'     =>  20,
                    ));

                    $invoiceLogo = new PHPExcel_Worksheet_Drawing;
                    $invoiceLogo->setPath(public_path("img/xbrl_logo_2.png"));
                    $invoiceLogo->setCoordinates('A2');
                    $invoiceLogo->setWorksheet($sheet);

                    $sheet->setSize(array(
                        'A2' => array(
                            'height'    => 45
                        )
                    ));

                    $sheet->cell('A5', function ($cell) {
                        $cell->setValue('TAX INVOICE');
                        $cell->setFontSize(24);
                    });

                    $sheet->cell('A6',  function ($cell) {
                        $cell->setValue('Invoice Date');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('B6', '1STOPXBRL');

                    if (count($jobs) == 1 && $company->assign_invoice_to_project_name) {
                        $sheet->cell('A14', $jobs[0]->project_name);
                    } else {
                        $sheet->cell('A14', $company->name);
                    }

                    $sheet->cell('A7', $date);
                    $sheet->cell('B7', '1STOPXBRL LIMITED');

                    $sheet->cell('A15', $company->address1);

                    $sheet->cell('A8',  function ($cell) {
                        $cell->setValue('Account Number');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('B8',  '601 International House');

                    // $sheet->cell('A8',  $company->address1);
                    $sheet->cell('A9',  $user->id);
                    $sheet->cell('B9',  '223 Regent Street');

                    $sheet->cell('A17',  $company->address2);
                    $sheet->cell('A18',  " " . $company->postcode);
                    $sheet->cell('A10',  function ($cell) {
                        $cell->setValue('Invoice Number');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B10',  'London');

                    $sheet->cell('A16',  $company->city);
                    $sheet->cell('A11', $newInvoiceNumber);
                    $sheet->cell('B11', 'W1B 2QD');

                    $sheet->cell('A12',  function ($cell) {
                        $cell->setValue('VAT Number');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B12',  'UNITED KINGDOM');

                    $sheet->cell('A17',  $country->name);
                    $sheet->cell('A13', ''); // Vat Number Here

                    $sheet->getStyle('A6:A13')->getAlignment()->applyFromArray(
                        array('horizontal' => 'left')
                    );

                    $sheet->cells('A20:F20', function ($cells) {
                        $cells->setBorder('thin', 'thin', 'medium', 'thin');
                    });



                    $sheet->cell('A20',  function ($cell) {
                        $cell->setValue('Description of Service Supplied');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B20',  function ($cell) {
                        $cell->setValue('Pages');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('C20',  function ($cell) {
                        $cell->setValue('Type');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('D20',  function ($cell) {
                        $cell->setValue('Client PO');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('E20',  function ($cell) {
                        $cell->setValue('Sub Total');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('F20',  function ($cell) {
                        $cell->setValue('Total (Inc. Vat)');
                        $cell->setFontWeight('bold');
                    });

                    $rowNumber = 21;
                    $subTotal = 0;
                    $priceSub = 0;
                    $dueDate = $jobs[0]->due_date;

                    foreach ($jobs as $job) {
                        $type = $job->tax_computed == 0 ? 'Statutory Account' : 'Tax Computation';
                        $pricing = $this->getJobPricing($job->page_count, $job->company, $job->turnaround, $type, $job->taxonomy_name);

                        // $price = isset($pricing[0]) ? $pricing[0]->total_price : $job->computed_price;
                        $price = $pricing['total_price'];

                        // if ($dueDate->gt($job->due_date)) {
                        //     $dueDate = $job->due_date;
                        // }

                        $tax_price = $price - $job->tax_computation_price;
                        $sheet->appendRow(array($job->project_name, $job->page_count . ' (' . $job->number_of_days . ' days) ', $type, $job->purchase_order, $tax_price, $price));

                        $sheet->cells('A' . $rowNumber . ':F' . $rowNumber, function ($cells) {
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $priceSub += $price;
                        $subTotal += $tax_price;
                        $rowNumber++;
                    }

                    // foreach($jobs as $job) {

                    //     // $pricingGridType = $company->pricing_grid;

                    //     // $pricing = Pricing::where('pages', $job->total_pages_submitted)
                    //     //     ->where('type', $pricingGridType)
                    //     //     ->orderBy('created_at', 'DESC')
                    //     //     ->first();

                    //     $price = $job->computed_price;
                    //     // $price = !empty($pricing) ? $pricing->price : 0;


                    //     if ($dueDate->gt($job->due_date)) {
                    //         $dueDate = $job->due_date;
                    //     }

                    //     $turnaround = Turnaround::where('id',$job->turnaround)->first();

                    //     $tax_price = $price+($price * 0.20);
                    //     $sheet->appendRow(array($job->project_name, $job->total_pages_submitted.' ('. $turnaround->number_of_days .' days) ','Type here!', $job->purchase_order, $price, $tax_price));

                    //     $sheet->cells('A'.$rowNumber.':F'.$rowNumber, function($cells) {
                    //         $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    //     });

                    //     $subTotal+= $price;
                    //     $rowNumber++;
                    // }
                    $rowNumber = $rowNumber + 1;
                    $sheet->cell('E' . $rowNumber, 'Subtotal');
                    $sheet->cell('F' . $rowNumber, $subTotal);

                    $totalVat = $priceSub - $subTotal;

                    $rowNumber++;
                    $sheet->cell('E' . $rowNumber, 'Total VAT 20%');
                    $sheet->cell('F' . $rowNumber, $totalVat); // Total Vat Here

                    // $sheet->cells('E'.$rowNumber.':F'.$rowNumber, function($cells) {
                    //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                    // });

                    $totalGbp = $totalVat + $subTotal;

                    $rowNumber++;
                    $sheet->cell('E' . $rowNumber, function ($cell) {
                        $cell->setValue('TOTAL GBP');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('F' . $rowNumber,  $totalGbp); // Total GBP Here

                    $rowNumber++;
                    // $sheet->cell('D'.$rowNumber,'Less Amount Paid');
                    // $sheet->cell('E'.$rowNumber, ''); // Less Amount Paid Here

                    // $sheet->cells('D'.$rowNumber.':E'.$rowNumber, function($cells) {
                    //     $cells->setBorder('medium', 'medium', 'medium', 'medium');
                    // });

                    $rowNumber++;
                    // $sheet->cell('F'.$rowNumber, function($cell) {
                    //     $cell->setValue('AMOUNT DUE');
                    //     $cell->setFontWeight('bold');
                    // });
                    // $sheet->cell('G'.$rowNumber, function($cell) {
                    //     $cell->setValue(''); // Amount Due Here
                    //     $cell->setFontWeight('bold');
                    // }); 

                    // $sheet->setColumnFormat(array(
                    //     'G20:G'.$rowNumber => '0.00'
                    // ));           

                    $sheet->getStyle('F20:G' . $rowNumber)->getAlignment()->applyFromArray(
                        array('horizontal' => 'right')
                    );
                    $rowNumber += 3;

                    $sheet->cell('A' . $rowNumber, function ($cell) use ($dueDate) {
                        $cell->setValue('Due Date: ' . date('d M Y', strtotime($dueDate))); // Due Date Here
                        $cell->setFontSize(11);
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('E' . $rowNumber, 'Registered in England & Wales No. 8072489');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Bank: Metro Bank');
                    $sheet->cell('E' . $rowNumber, 'Vat Number 136 3220 45');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Branch: One Southampton Row, London WC1B 5HA');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Acct Name: 1STOPXBRL LIMITED');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Sort Code: 23-05-80');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Account Number: 11039758');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'IBAN: GB56MYMB23058011039758');
                    $rowNumber++;

                    $sheet->cell('A' . $rowNumber, 'Swift: MYMBGB2L');
                    $rowNumber += 3;

                    $sheet->cell('A' . $rowNumber, 'Specialists in iXBRL tagging and E-Filing. Recognised by HMRC and Companies House. HMRC Vendor ID1698 - HMRC eFiling');

                    $sheet->getStyle('A' . $rowNumber)->getAlignment()->setWrapText(true);
                    $rowNumber += 11;

                    $sheet->cell('A' . $rowNumber, function ($cell) {
                        $cell->setValue('Company Registration No: 8072489. Registered Office: Attention: 1STOPXBRL LIMITED, 601 International House, 223 Regent Street, London, London, W1B 2QD, United Kingdom.'); // Due Date Here
                        $cell->setFontSize(7);
                    });
                });
            })->store('xls', $targetFullStoragePath);

            $gcsFileName = $newInvoiceNumber . '.xls';
            $fileName = $name . '.xls';
            $file = $targetFullStoragePath . '/' . $fileName;
            $fileContents = file_get_contents($file);

            try {

                $storage = Storage::disk('gcs');
                $storage->put('invoices/' . $gcsFileName, $fileContents);

                $responseArray = ['filename' => $fileName, 'gcs_filename' => $gcsFileName, 'status' => '200'];
                return $responseArray;
            } catch (\Exception $e) {

                File::delete($file);

                $responseArray = ['error' => 'Failed to generate invoice', 'status' => '200'];
                return $responseArray;
            }
        }
    }
}
