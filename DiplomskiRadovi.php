<?php
require_once "iRadovi.php";
require_once "Database.php";
require_once "HtmlParser.php";
require_once "HttpClient.php";

class DiplomskiRadovi implements iRadovi {
    const BASE_URL = 'https://stup.ferit.hr/index.php/zavrsni-radovi/page/';

    private $pdo;

    private $radovi = [];

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function create() {
        for ($i = 2; $i <= 6; $i++) {
            $html = HttpClient::get($this->generateFetchUrl($i));

            if ($html) {
                $this->radovi = array_merge($this->radovi, HtmlParser::parseHtml($html));
            }
        }
    }

    public function save() {
        $stmt = $this->pdo->prepare("INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES (:naziv, :tekst, :link, :oib)");

        foreach ($this->radovi as $rad) {
            $stmt->execute([
                ':naziv' => $rad['naziv_rada'],
                ':tekst' => $rad['tekst_rada'],
                ':link' => $rad['link_rada'],
                ':oib' => $rad['oib_tvrtke']
            ]);
        }
    }

    public function read() {
        $stmt = $this->pdo->query("SELECT * FROM diplomski_radovi");
        $radovi = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html = [];

        foreach ($radovi as $rad) {
            $html[] = " <b>Naziv rada:</b> {$rad['naziv_rada']} <br>
                        <b>Link rada:</b> <a href='{$rad['link_rada']}'>{$rad['link_rada']}</a><br>
                        <b>OIB tvrtke:</b> {$rad['oib_tvrtke']} <br>";
        }

        return implode("<hr>", $html);
    }

    private function generateFetchUrl($i)
    {
        return  self::BASE_URL . $i;
    }
}