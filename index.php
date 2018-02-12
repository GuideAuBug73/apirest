<?php
include "tp2-helpers.php";

$lines = file("wifi.csv");      //On lit le fichier CSV
$nb = count($lines) - 1;                //On compte le nombre de ligne du fichier
echo "Nombre de ligne: " . $nb; //affichage du nombre de ligne


$csv = array_map('str_getcsv', file("wifi.csv"));   //Créer un tableau, chaque élément contient une ligne
array_walk($csv, function (&$a) use ($csv) {    //Appel de la fonction array_combine pour chaque élément de $csv
    $a = array_combine($csv[0], $a);        //Créer un tableau, chaque élément contient une colonne de la ligne puis le remet dans le premier tableau -> cela donne un tableau de tableau
});
array_shift($csv);      //Supprime la première ligne ne contenant pas de données utile

$grenette = geopoint(5.72752, 45.19102); // Les coordonees de place grenette

echo "<br><br>";

$near = 99999999999;    // Variable qui va nous servir a comparer les distance
for ($i = 0; $i < $nb; $i++) { //On lit toute les lignes du fichier CSV
    $lon = $csv[$i]["longitude"];   //On recupere la longitude de $j
    $lat = $csv[$i]["latitude"];    //On recupere la latitude de $j
    $coord = geopoint($lon, $lat);  //on recupere ces coordonees
    $dist = round(distance($coord, $grenette), 1);  //on recupere la distance enntre l'AP et notre point d'origine (le tout arrondi a 0.1 metres)
    if ($dist <= 200) { //on va afficher seulement les AP a une distance inferieur ou egal a 200m
       echo "Le point d'acces " . $csv[$i]["Nom"] . "  se trouve a une distance de: " . $dist . " metres <br><br>"; // et enfin on affiche les informations
        if ($dist < $near) {    //on cherche l'AP le plus proche de nous
            $near = $dist;
            $save = $i; //On sauvegarde sa position dans $csv
        }
    }
}

echo "Le point d'acces le plus proche est: " . $csv[$save]["Nom"]; //Affichage de l'AP le plus proche


?>