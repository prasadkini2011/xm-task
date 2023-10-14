<?php
namespace App\Services;

class FormatDataService
{
    public function formatDataForChart($data){
        $returnData = [];
        if(!empty($data['prices']) && count($data['prices']) > 0){
            foreach ($data['prices'] as $key => $value) {
                if(!isset($value['open'])){
                    continue;
                }
                $returnData[] = [
                    'x' => $value['date'] * 1000,
                    'y' => [$value['open'],$value['high'],$value['low'],$value['close']]
                ];
            }
        }
        return $returnData;
     }
}