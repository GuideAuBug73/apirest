<?php
$lon = $_GET['lon'];
$lat = $_GET['lat'];
$top = $_GET['top'];
if (empty($lon) or empty($lat) or empty($top)) {
    print("<center>L'url est invalide (Les coordonnees ou nombre de point d'acces a afficher manque exemple: 'lon=5.72752 lat=45.19102 top=5')</center>"); //On verifie que la variable est declare sinon on arrete tout.
    exit();
}
include "tp2-helpers.php";

$lines = file("wifi.csv");      //On lit le fichier CSV
$nb = count($lines) - 1;                //On compte le nombre de ligne du fichier

$csv = array_map('str_getcsv', file("wifi.csv"));   //Créer un tableau, chaque élément contient une ligne
array_walk($csv, function (&$a) use ($csv) {    //Appel de la fonction array_combine pour chaque élément de $csv
    $a = array_combine($csv[0], $a);        //Créer un tableau, chaque élément contient une colonne de la ligne puis le remet dans le premier tableau -> cela donne un tableau de tableau
});
array_shift($csv);      //Supprime la première ligne ne contenant pas de données utile

$localistation = geopoint($lon, $lat); //Localisation d'origine
$arraysort = array();
for ($i = 0; $i < $nb; $i++) {
    $lon = $csv[$i]["longitude"];
    $lat = $csv[$i]["latitude"];
    $coord = geopoint($lon, $lat); //Coordonnees de l'AP
    $dist = round(distance($coord, $localistation), 1); //Calcul de la distance entre les deux points
    $arraysort += [$dist => $i];       // On cree un tableau ayant pour clée la distance et pour valeur sa position dans le tableau $csv
}
ksort($arraysort);   //On trie le tableau par rapport a leur clée

$tableau = array();   //Tableau dans lequel serons place les positions des AP dans $csv
foreach ($arraysort as $value) {
    array_push($tableau, $value);  // On remplit le tableau
}

$count = count($tableau);
$test = true;
if ($count < $top) {    //test afin de verifier que le $top n'est pas trop grand par rapport au nombre de donnees dans le tableau
    print("<center>Le nombre d'objet a afficher est trop grand</center>");
    $test = false;
}

$tmp = array();
$tabjson = array();
for ($i = 0; $i < $top; $i++) {    //On va afficher le nombre d'AP en fonction de $N
    $j = $tableau[$i];      //On recupere la posistion de des AP dans le tableau $csv
    $lon = $csv[$j]["longitude"];   //On recupere la longitude de $j
    $lat = $csv[$j]["latitude"];    //On recupere la latitude de $j
    $coord = geopoint($lon, $lat);  //on recupere ces coordonnees
    $dist = round(distance($coord, $localistation), 1);  //on recupere la distance enntre l'AP et notre point d'origine (le tout arrondi a 0.1 metres)
    $data = smartcurl("https://api-adresse.data.gouv.fr/reverse/?lon=" . $lon . "&lat=" . $lat, 1); //On recupere les donnee du site api-adresse.data.gouv.fr
    $parsed_json = json_decode($data);          //On decode le json
    $adresse = $parsed_json->{"features"}[0]->{"properties"}->{"label"};    //On recupere l'addresse en fcntion des coordonnees
    $tmp = array('type' => 'Feature', 'Ok' => $test, 'geometry' => array('type' => 'Point', 'coordinates' => array($lon, $lat)), 'properties' => array('nom' => $csv[$j]['Nom'], 'distance' => $dist, 'adresse' => $adresse)); //On cree un tableau contenant les info de l'AP $j
    array_push($tabjson, $tmp);  //On ajoute ce tableau dans un tableau contenant les $top AP plus proche
}
echo json_encode($tabjson); //On traduit le tableau en json et on l'affiche
?>