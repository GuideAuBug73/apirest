<?php
$N = $_GET['N']; // Nombre d'AP a afficher
if (empty($N)) {
    print("<center>L'url est invalide (Le nombre de point d'acces a afficher manque exemple: 'N=5')</center>");     //On verifie que la variable est declare sinon on arrete tout.
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

$grenette = geopoint(5.72752, 45.19102); // Les nes de place grenette
$near = 99999999999;    // Variable qui va nous servir a comparer les distance
$arraysort = array();   //Tableau qui nous permettra de trier les distance
for ($i = 0; $i < $nb; $i++) {
    $lon = $csv[$i]["longitude"];
    $lat = $csv[$i]["latitude"];
    $coord = geopoint($lon, $lat);
    $dist = round(distance($coord, $grenette), 1);
    $arraysort += [$dist => $i];       // On cree un tableau ayant pour clée la distance et pour valeur sa position dans le tableau $csv
}
ksort($arraysort);   //On trie le tableau par rapport a leur clée
$tableau = array();   //Tableau dans lequel seras place les position des AP dans $csv
foreach ($arraysort as $value) {
    array_push($tableau, $value);  // On remplit le tableau
}

for ($i = 0; $i < $N; $i++) {    //On va afficher le nombre d'AP en fonction de $N
    $j = $tableau[$i];      //On recupere la posistion de des AP dans le tableau $csv
    $lon = $csv[$j]["longitude"];   //On recupere la longitude de $j
    $lat = $csv[$j]["latitude"];    //On recupere la latitude de $j
    $coord = geopoint($lon, $lat);  //on recupere ces coordonnees
    $dist = round(distance($coord, $grenette), 1);  //on recupere la distance enntre l'AP et notre point d'origine (le tout arrondi a 0.1 metres)
    $data = smartcurl("https://api-adresse.data.gouv.fr/reverse/?lon=" . $lon . "&lat=" . $lat, 1); //On recupere les donnee du site api-adresse.data.gouv.fr
    $parsed_json = json_decode($data);          //On decode le json
    $adresse = $parsed_json->{"features"}[0]->{"properties"}->{"label"};    //On recupere l'addresse en fcntion des coordonnees
    echo "Le point d'acces " . $csv[$j]["Nom"] . " se trouve a une distance de: " . $dist . " metres il a pour adresse: " . $adresse . "<br><br>";  // et enfin on affiche les informations
    if ($dist < $near) {       //on cherche l'AP le plus proche de nous
        $near = $dist;
        $save = $j;     //On sauvegarde sa position dans $csv
    }
}
echo "<br>";
echo "Le point d'acces le plus proche est: " . $csv[$save]["Nom"]; //Affichage de l'AP le plus proche


?>