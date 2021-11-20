<?php

// luodaan tietokantayhteys ja ilmoitetaan mahdollisesta virheestä

$y_tiedot = "dbname=vgtoui user=vgtoui password=2kZtJZLKjc5IyfT";

if (!$yhteys = pg_connect($y_tiedot))
   die("Tietokantayhteyden luominen epäonnistui.");

if (isset($_POST['tilisiirto']))
{
    $veloitettava_tilinumero = intval($_POST['veloitettava_tilinumero']);
    $kohdetilinumero = intval($_POST['kohdetilinumero']);
    $siirtosumma = floatval($_POST['summa']);

    $query = "UPDATE TILIT SET summa = summa - $siirtosumma WHERE tilinumero = '$veloitettava_tilinumero';";
    $query .= "UPDATE TILIT SET summa = summa + $siirtosumma WHERE tilinumero = '$kohdetilinumero';";	

    // jos kenttiin on syötetty jotain, lisätään tiedot kantaan

    $tiedot_ok = $veloitettava_tilinumero != 0 && $kohdetilinumero != 0 && $siirtosumma != 0;

    if ($tiedot_ok)
    {
        $paivitys = pg_query($query);

        if ($paivitys && (pg_affected_rows($paivitys) > 0))
            $viesti = "Summa $siirtosumma on siirretty tililt䠻$veloitettava_tilinumero} tilille {$kohdetilinumero}!";
        else
            $viesti = 'Summaa ei siirretty: ' . pg_last_error($yhteys);
    }
    else
        $viesti = 'Annetut tiedot puutteelliset - tarkista, ole hyvä!';
}

// suljetaan tietokantayhteys

pg_close($yhteys);

?>

<html>
 <head>
  <title>Tilisiirto</title>
 </head>
 <body>
    <!-- Lomake lähetetään samalle sivulle (vrt lomakkeen kutsuminen) -->
    <form action="tilisiirto.php" method="post">
    <h2>Tilisiirto</h2>
    <?php if (isset($viesti)) echo '<p style="color:red">'.$viesti.'</p>'; ?>
	<form method="post" action="tilisiirto.php">
	   <label>Summa:  <input type="number" min="0.1" step="0.1" max="999999999" size="9" name="summa"/></label>
	   <label>Veloitettavan tilinumero:
 	      <select name="veloitettava_tilinumero">
	         <option value="123456789">123456789</option>
		 <option value="987654321">987654321</option>
	      </select>
	   <label>
	   <label>Kohdetilinumero:
 	      <select name="kohdetilinumero">
	         <option value="123456789">123456789</option>
		 <option value="987654321">987654321</option>
	      </select>
	   <label>
       	   <input type="submit" name="tilisiirto" value="Siirt䤢 />
	</form>
</body>
</html>
