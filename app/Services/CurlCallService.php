<?php
namespace App\Services;
use Illuminate\Support\Facades\Log;

class CurlCallService
{
    public function makeCurlCall($url)
    {
        // Initialize cURL
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_CAINFO => base_path('cacert.pem'),
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host:". env('RAPID_HOST'),
                "X-RapidAPI-Key: ". env('RAPID_KEY')
            ],
        ]);

        $response = curl_exec($curl);

        // Handle any cURL errors
        if (curl_errno($curl)) {
            Log::error(curl_error($curl));
            throw new \Exception('cURL Error: ' . curl_error($curl));
        }

        // Close cURL session
        curl_close($curl);

        return $response;
    }
}