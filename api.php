<?php

session_start();
error_reporting(E_ALL & ~E_NOTICE);         // schakel notices uit (zijn niet interessant maar mollen de JSON output wel)
header('Access-Control-Allow-Origin: *');  // kan ook van andere servers dan alleen localhost benaderd worden
header('Content-Type: application/json');  // geef aan dat deze file een JSON format teruggeeft

$servername = "localhost";
$username = "root";
$password = "";
$database = "vue";

// Create connection
$con = new mysqli($servername, $username, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// standaard json output zodat api altijd wat terug geeft (tenzij er fouten in SQL of PHP zitten
$json = array(
    "sMessage" => "nog niets geset",
    "bSuccess" => false,
    "data" => null
);

// bier: `id`, `naam`, `brouwer`, `type`, `gisting`, `perc`

// via de URL: https:// [pad naar API] api.php?action=getBeer kun je biertjes ophalen
// verwacht GET variabele action en POST variabele
if ($_GET["action"] == "getBeer") {
    $sql = "SELECT * FROM bier ORDER BY id";
    $res = mysqli_query($con, $sql);
    if ($res) {
        $lst = array();
        while ($rij = mysqli_fetch_assoc($res)) {
            $lst[] = array_map("utf8_encode", $rij); // zorgt dat foute characterset omgezet wordt naar UTF8
        }
        $json = array(
            "sMessage" => "Biertjes zijn opgehaald",
            "bSuccess" => true,
            "data" => $lst
        );
    } else {
        $json = array(
            "sMessage" => "Biertjes zijn NIET opgehaald. SQL: " . $sql,
            "bSuccess" => false,
            "data" => null
        );
    }
}

// via de URL: https:// [pad naar API] api.php?action=updateBeer kun je biertjes updaten
// verwacht GET variabele action en POST variabele $_POST met alle veld-inhoud
if ($_GET["action"] == "updateBeer") {
    //bier: `id`, `naam`, `brouwer`, `type`, `gisting`, `perc`
    $sql = "UPDATE bier SET naam = '" . $_POST["naam"] . "', brouwer = '" . $_POST["brouwer"] . "', type = '" . $_POST["type"] . "', gisting = '" . $_POST["gisting"] . "', perc = '" . $_POST["perc"] . "' WHERE id = '" . $_POST["id"] . "';";
    $res = mysqli_query($con, $sql);
    if ($res) {

        $json = array(
            "sMessage" => "Bier is aangepast",
            "bSuccess" => true,
            "data" => null
        );
        header("Refresh:0");
    } else {
        $json = array(
            "sMessage" => "Biertjes zijn NIET ge-update. SQL: " . $sql, // als de query fout gaat geeft hij de sql terug, handig voor ontwikkelaar
            "bSuccess" => false,
            "data" => null
        );
    }
}

if ($_GET["action"] == "deleteBeer") {
    $sql = "DELETE FROM bier WHERE id = '" . $_POST["id"] . "';";
    $res = mysqli_query($con, $sql);
    if ($res) {
        $json = array(
            "sMessage" => "Bierje is gewist",
            "bSuccess" => true,
            "data" => null
        );
    } else {
        $json = array(
            "sMessage" => "Biertje is niet gewist. SQL: " . $sql,
            "bSuccess" => false,
            "data" => null
        );
    }
}

// if ($_GET["action"] == "insertBeer") {
//     // verzin hem zelf ;-)
// }

echo json_encode($json);
