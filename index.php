<?php


    $url = "http://module1.loc/";
    $curl = curl_init();

    $opt = array('name' => 'value'); 

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $opt);
   
    curl_exec($curl);
    curl_close($curl);


