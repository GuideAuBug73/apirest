<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="test.css">
    <title>API Point d'acces</title>
</head>
<body>
<center><h1>Rechercher les points d'acces les plus proches</h1></center>
<form method="get" action="api.php">
    <p>Entrez les informations necessaires<br>( Ex: Lon: 5.72752 Lat: 45.19102 )</p>
    <label for="lon">Longitude: </label>
    <input type="text" name="lon" placeholder="Longitude" required value="5.72752"><br>
    <label for="lat">Latitude: </label>
    <input type="text" name="lat" placeholder="Latitude" required value="45.191102"><br>
    <label for="top">Nombre d'AP a afficher</label>
    <input type="number" name="top" placeholder="Nombre" required value="5"><br>
    <label for="data">Choix des donnees</label>
    <select name="data">
        <option value="wifi">Point d'acces WiFi</option>
        <option value="ORA">Antennes GSM Orange</option>
        <option value="BYG">Antennes GSM Bouygues</option>
        <option value="SFR">Antennes GSM SFR</option>
        <option value="FREE">Antennes GSM Free</option>
    </select><br><br>
    <input type="submit" value="Envoyer">
</form>
</body>
</html>