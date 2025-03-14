<?php
require_once 'HtmlParser.php';
require_once 'DiplomskiRadovi.php';

$radovi = new DiplomskiRadovi();
$radovi->create();
$radovi->save();
echo($radovi->read());
echo "Uspjesno kreirano, spremljeno i procitano";