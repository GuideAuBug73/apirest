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

if ($typedata == 'wifi') { //Si on choisi les point d'acces wifi
    $data = smartcurl("http://192.168.227.128/webservice.php/?lon=" . $lon . "&lat=" . $lat . "&top=" . $top, 1); //on recupere les donnees de webservices.php
    $type = "points d'acces"; //Variable pour l'affichage
    $nomOPE = ""; //Variable pour l'affichage
} elseif ($typedata == 'ORA' or $typedata == 'BYG' or $typedata == 'SFR' or $typedata == 'FREE') { //Si on choisi les antennes GSM
    $data = smartcurl("http://192.168.227.128/webserviceGSM.php/?lon=" . $lon . "&lat=" . $lat . "&top=" . $top . "&data=" . $typedata, 1); // on recupere les donnees de webservicesGSM.php
    $type = "antennes GSM"; //Variable pour l'affichage
    if ($typedata == 'ORA') {  //Variable pour l'affichage
        $nomOPE = " d'Orange";
    } elseif ($typedata == 'BYG') { //Variable pour l'affichage
        $nomOPE = ' de Bouygues';
    } elseif ($typedata == 'SFR') { //Variable pour l'affichage
        $nomOPE = " de SFR";
    } elseif ($typedata == 'FREE') { //Variable pour l'affichage
        $nomOPE = " de FREE";
    }
} else {
    exit();
}
$parsed_json = json_decode($data); //On decode le json
$test = $parsed_json[0]->{'Ok'}; //on recupere la variable pour savoir si le $top n'est pas trop grand par rapport au nombre d'objet a afficher
if ($test != 1) {
    print("<center>Le nombre d'objet a afficher est trop grand</center>");
    exit();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="test.css">
    <title>API Point d'acces</title>
</head>
<body id="bodyApi">
<center><h1>Voici la liste des <?= $top ?> <?= $type ?><?= $nomOPE ?> les plus proches des coordonnees:<br>
        Longitude: <?= $lon ?> Latitude: <?= $lat ?></h1></center>
<table>
    <thead>
    <td>Nom</td>
    <td>Distance (metres)</td>
    <td>Adresse</td>
    </thead>
    <tbody>
    <?php
    for ($i = 0; $i < $top; $i++) {
        $nom = $parsed_json[$i]->{"properties"}->{"nom"}; //on recupere le nom dans les donnees Json
        $dist = $parsed_json[$i]->{"properties"}->{"distance"}; //on recupere la distance dans les donnees Json
        $adresse = $parsed_json[$i]->{"properties"}->{"adresse"}; //on recupere l'adresse dans les donnees Json
        ?>
        <tr>
            <td><?= $nom ?></td>
            <td><?= $dist ?></td>
            <td><?= $adresse ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</body>
</html>