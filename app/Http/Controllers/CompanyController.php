<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendEmail;
use App\Rules\CompanySymbolCheck;
use App\Rules\DateFormatCheck;
use App\Services\CurlCallService;
use App\Services\FormatDataService;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    public function getStockData(Request $request, CurlCallService $curlService,FormatDataService $formatData){
        try{
            $validator = Validator::make($request->all(), [
                'companySymbol' => ['required',new CompanySymbolCheck],
                'startDate' => ['required','date','before_or_equal:today','before_or_equal:endDate', new DateFormatCheck],
                'endDate' => ['required','date','before_or_equal:today','after_or_equal:startDate', new DateFormatCheck],
                'email' => 'required|email',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
        
            $url = env('RAPID_API_URL').'?symbol='.$request->companySymbol.'&region=US';
            $response = $curlService->makeCurlCall($url);
            $responseDecode = !empty($response) ? json_decode($response,true) : [];
            if(empty($responseDecode['message'])){
                $mailData = [
                    'companySymbol' => strtoupper($request->companySymbol),
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate
                ];
                Mail::to($request->email)->send(new SendEmail($mailData));

                $chartData = $formatData->formatDataForChart($responseDecode);
                
                return response()->json(['status' => true,'message' => 'Success','data' => $responseDecode,'chartData' => $chartData]);
            }else{
                return response()->json(['status' => false,'message' => 'Some Error Occured. try again later.']);
            }
        }catch(Exception $e){
            Log:error($e);
        }
     }
}
