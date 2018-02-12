<?php

/**
 * @param float $lon
 * @param float $lat
 * @return array ['lon'=>float, 'lat'=>float]
 */
function geopoint($lon, $lat) {
  return ['lon'=>$lon, 'lat'=>$lat];
}

/* 
   @param $p array('lon'=>int, 'lat'=>int)
   @param $q array('lon'=>int, 'lat'=>int)
   @return int distance (approx.) in meters
*/
function distance ($p, $q) {
  $scale = 10000000 / 90; // longueur d'un degré le long d'un méridiene
  $a = ($p['lon'] - $q['lon']);
  $b = (cos($p['lat']/180.0*M_PI) * ($p['lat'] - $q['lat']));
  $res = $scale * sqrt( pow($a,2) + pow($b,2) );
  return $res;
}

/**
 * curl wrapper
 * @param string $url
 * @param int verb verbosity 0, 1, 2
 * @return string $content
 **/  
  
function smartcurl($url, $verb) {
    $ch = curl_init();
	
	if ($verb == 2) { echo "$url\n"; }
    if ($verb == 1) { echo "" ; }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    curl_close($ch);      

    return $output;
}




/*  return a standard accesspoint structure
    @return array $accesspoint
*/
function initAccesspoint() {
  return array(
      'name' => null, //string
      'adr' => null,  //string
      'lon' => null,  //float, in decimal degrees
      'lat' => null   //float, in decimal degrees
     );  
}
