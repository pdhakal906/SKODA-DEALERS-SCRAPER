<?php
include 'phpQuery-onefile.php';
$names = [];
$addresses = [];
$phone_numbers = [];
$dealer_names = [];

function extractData($names, $addresses, $phone_numbers, $dealer_names){
    for($i = 0; $i < 2; $i++){
        $url = "https://dealers.skoda-auto.co.in/location/rajasthan";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        
        $document = phpQuery::newDocument($resp);
        foreach($document->find('div.outlet-list div.store-info-box') as $item){
            $title = trim($document->find('ul div.info-text', $item)->text());
            $address = trim($document->find('li.outlet-address', $item)->text());
            $number = trim($document->find('li.outlet-phone', $item)->text());
            array_push($names, $title);
            array_push($addresses, $address);
            array_push($phone_numbers, $number);
        }
        
        if($i == 1){
            $url = "https://dealers.skoda-auto.co.in/location/rajasthan?&page=2";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $document = phpQuery::newDocument($resp);
            foreach($document->find('div.outlet-list div.store-info-box') as $item){
                $title = trim($document->find('ul div.info-text', $item)->text());
                $address = trim($document->find('li.outlet-address', $item)->text());
                $number = trim($document->find('li.outlet-phone', $item)->text());
                array_push($names, $title);
                array_push($addresses, $address);
                array_push($phone_numbers, $number);
            }   
    
        }
    
    
    }
    getName($names, $addresses, $phone_numbers, $dealer_names);

}
function getName($names, $addresses, $phone_numbers, $dealer_names){
    for ($i = 0; $i < count($names); $i++){
        $str = preg_replace("/[\r\n]*/","",$names[$i]);
        $matches = [];
        $pattern = "/(?<=\s\s\s).*(?=East|West|North|South|CNCR|)/i";
        preg_match($pattern, $str, $matches);
        $dealer_name = trim($matches[0]);
        array_push($dealer_names, $dealer_name);
    }
    writeData($addresses, $phone_numbers, $dealer_names);
}

function writeData($addresses, $phone_numbers, $dealer_names){
    $file = fopen('dealers.csv', 'w');
    fputcsv($file, array("Dealer Name", "Address", "Phone"));
    $final_array = [];

    for($i = 0; $i < count($dealer_names); $i++){
    array_push($final_array, array($dealer_names[$i],$addresses[$i], $phone_numbers[$i]));
    }

    foreach($final_array as $final){
    fputcsv($file, $final);
    }
}

extractData($names, $addresses, $phone_numbers, $dealer_names);


?>