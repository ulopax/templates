<?php

function get_location($lat, $lon)
{
    $lat = number_format($lat, 6);
    $lon = number_format($lon, 6);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/50.0.2661.102 Chrome/50.0.2661.102 Safari/537.36');
    curl_setopt($curl, CURLOPT_REFERER, 'http://botshackathon.com/');
    $output = json_decode(curl_exec($ch), true);

    curl_close($ch);
    return $output;
}
