<?php
$lon = $_GET['lon'];
$lat = $_GET['lat'];
$top = $_GET['top'];
$typedata = $_GET['data'];
if (empty($lon) or empty($lat) or empty($top) or empty($typedata)) {
    print("<center>L'url est invalide (Les coordonnees ou nombre de point d'acces a afficher manque exemple: 'lon=5.72752 lat=45.19102 top=5')</center>"); //On verifie que la variable est declare sinon on arrete tout.
    exit();
}

include "tp2-helpers.php";

$data = smartcurl("http://sig.grenoble.fr/opendata/Antenne_GSM/json/DSPE_ANT_GSM_EPSG4326.json", 1);
$parsed_json = json_decode($data); //On decode le json

$localistation = geopoint($lon, $lat); //Localisation d'origine
$arraysort = array();
for ($i = 0; $i < 100; $i++) {
    $operateur = $parsed_json->{"features"}[$i]->{"properties"}->{"OPERATEUR"};
    if ($operateur == $typedata) {   //On choisi seulement les donnees de l'operateur choisi
        $lon = $parsed_json->{"features"}[$i]->{"geometry"}->{"coordinates"}[0];
        $lat = $parsed_json->{"features"}[$i]->{"geometry"}->{"coordinates"}[1];
        $coord = geopoint($lon, $lat); //Coordonnees de l'antenne
        $dist = round(distance($coord, $localistation), 1); //Calcul de la distance entre les deux points
        $arraysort += [$dist => $i];       // On cree un tableau ayant pour clée la distance et pour valeur sa position dans le tableau $csv
    }
}
ksort($arraysort);   //On trie le tableau par rapport a leur clée

$tableau = array();   //Tableau dans lequel serons place les positions des AP dans $csv
foreach ($arraysort as $value) {
    array_push($tableau, $value);  // On remplit le tableau
}

$count = count($tableau);
$test = true;
if ($count < $top) { //test afin de verifier que le $top n'est pas trop grand par rapport au nombre de donnees dans le tableau
    print("<center>Le nombre d'objet a afficher est trop grand</center>");
    $test = false;
}

$tmp = array();
$tabjson = array();
for ($i = 0; $i < $top; $i++) {    //On va afficher le nombre d'AP en fonction de $N
    $j = $tableau[$i];      //On recupere la posistion de des AP dans le tableau $csv
    $lon = $parsed_json->{"features"}[$j]->{"geometry"}->{"coordinates"}[0];   //On recupere la longitude de $j
    $lat = $parsed_json->{"features"}[$j]->{"geometry"}->{"coordinates"}[1];   //On recupere la latitude de $j
    $coord = geopoint($lon, $lat);  //on recupere ces coordonnees
    $dist = round(distance($coord, $localistation), 1);  //on recupere la distance enntre l'AP et notre point d'origine (le tout arrondi a 0.1 metres)
    $adresse = $parsed_json->{"features"}[$j]->{"properties"}->{"ANT_ADRES_LIBEL"};    //On recupere l'addresse en fcntion des coordonnees
    $antID = $parsed_json->{"features"}[$j]->{"properties"}->{"ANT_ID"}; //On recupere l'identifant d'antenne
    $tmp = array('type' => 'Feature', 'Ok' => $test, 'geometry' => array('type' => 'Point', 'coordinates' => array($lon, $lat)), 'properties' => array('nom' => $antID, 'distance' => $dist, 'adresse' => $adresse)); //On cree un tableau contenant les info de l'AP $j
    array_push($tabjson, $tmp);  //On ajoute ce tableau dans un tableau contenant les $top AP plus proche
}
echo json_encode($tabjson); //On traduit le tableau en json et on l'affiche
?>