<?php
$lon = $_GET['lon'];
$lat = $_GET['lat'];
$top = $_GET['top'];
if (empty($lon) or empty($lat) or empty($top)) {
    print("<center>L'url est invalide (Les coordonnees ou nombre de point d'acces a afficher manque exemple: 'lon=5.72752 lat=45.19102 top=5')</center>"); //On verifie que la variable est declare sinon on arrete tout.
    exit();
}
include "tp2-helpers.php";
$data = smartcurl("http://192.168.227.128/webservice.php/?lon=" . $lon . "&lat=" . $lat . "&top=" . $top, 1);
$parsed_json = json_decode($data);
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
<center><h1>Voici la liste des <?= $top ?> points d'acces les plus proches des coordonnees:<br> Longitude: <?= $lon ?> Latitude: <?= $lat ?></h1></center>
<table>
    <thead>
    <td>Nom</td>
    <td>Distance (metres)</td>
    <td>Adresse</td>
    </thead>
    <tbody>
    <?php
    for ($i = 0; $i < $top; $i++) {
        $nom = $parsed_json[$i]->{"properties"}->{"nom"};
        $dist = $parsed_json[$i]->{"properties"}->{"distance"};
        $adresse = $parsed_json[$i]->{"properties"}->{"adresse"};
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