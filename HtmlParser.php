<?php
require_once('HttpClient.php');

class HtmlParser {
    public static function parseHtml($html)
    {
        if (empty($html)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $html = '<?xml encoding="UTF-8">' . $html;
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $posts = $xpath->query('//div[contains(@class, "post")]');

        $radovi = [];

        foreach ($posts as $post) {
            $titleNodes = $xpath->query('.//h2[contains(@class, "post-title")]/a', $post);
            if ($titleNodes->length === 0) continue;

            $titleNode = $titleNodes->item(0);
            if (!($titleNode instanceof DOMElement)) continue;
            $naziv_rada = trim($titleNode->textContent);
            $link_rada = $titleNode->getAttribute('href');

            $imgNodes = $xpath->query('.//img[contains(@class, "wppost-image")]/@src', $post);
            $oib_tvrtke = 'N/A';
            if ($imgNodes->length > 0) {
                $imgSrcNode = $imgNodes->item(0);
                if ($imgSrcNode instanceof DOMAttr) {
                    $imgSrc = $imgSrcNode->value;
                    preg_match('/(\d+)\.png$/', $imgSrc, $matches);
                    $oib_tvrtke = isset($matches[1]) ? $matches[1] : 'N/A';
                }
            }

            $tekst_rada = self::fetchTextFromPage($link_rada);

            $radovi[] = [
                'naziv_rada' => $naziv_rada,
                'tekst_rada' => $tekst_rada,
                'link_rada' => $link_rada,
                'oib_tvrtke' => $oib_tvrtke
            ];
        }

        return $radovi;
    }

    public static function fetchTextFromPage($url) {
        $html = HttpClient::get($url);
        if (!$html) return "N/A";

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $html = '<?xml encoding="UTF-8">' . $html;
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $contentNodes = $xpath->query('//div[contains(@class, "post-content")]/p');

        return $contentNodes->length > 0 ? trim($contentNodes->item(0)->textContent) : "Nema opisa";
    }
}