<?php

namespace Kriuchaveta\Sitemap;

use Exception;

class CreateSiteMap
{
    const SITE_URL = ''; // url сайта для парсинга
    const XML = 'xml';
    const CSV = 'csv';
    const JSON = 'json';
    protected array $all_links;
    protected string $path;
    protected string $type;

    public function __construct(array $all_links, string $path, string $type)
    {
        $this->all_links = $all_links;
        $this->path = $path;
        $this->type = $type;
    }

    public function generateType($type): void
    {
        try {
            switch ($this->type) {
                case self::XML:
                    $this->generateXML();
                    break;
                case self::CSV:
                    $this->generateCSV();
                    break;
                case self::JSON:
                    $this->generateJSON();
                    break;
                default:
                    echo('Формат не определен:' . $type);
                    break;
            }
            echo 'Файл создан';
        } catch (Exception $e) {
            echo 'Выброшено исключение: ' . $e->getMessage();
        }
    }

    private function generateXML(): void
    {
        $dom = new \DomDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xsi:schemaLocation', "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");

        $dom->appendChild($root);

        $sxe = simplexml_import_dom($dom);

        $date = (new \DateTime())->format('Y-m-d');

        foreach ($this->all_links as $item) {
            $elem = trim(mb_substr($item, mb_strlen(self::SITE_URL)), '/');
            $elem = explode("/", $elem);

            $count = count($elem);
            if ($count == 1) {
                $priority = '1.0';
            } elseif ($count == 2) {
                $priority = '0.5';
            } else {
                $priority = '0.1';
            }

            if ($priority == '1.0') {
                $changefreq = 'hourly';
            } elseif ($priority == '0.5') {
                $changefreq = 'daily';
            } else {
                $changefreq = 'weekly';
            }

            $urlMain = $sxe->addChild('url');
            $urlMain->addChild('loc', htmlspecialchars($item));
            $urlMain->addChild('lastmod', $date);
            $urlMain->addChild('priority', $priority);
            $urlMain->addChild('changefreq', $changefreq);

        }
        $dom->appendChild($root);
        $this->path = preg_replace('/\/sitemap.xml\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)) {
                mkdir($this->path, 0777, true);
            }
            $dom->save($this->path . '/sitemap.xml');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    function generateCSV(): void
    {
        foreach ($this->all_links as $data) {
            $csv = $csv . $data['loc'] . ';' . $data['lastmod'] . ';' . $data['priority'] . ';' . $data['changefreq'] . "\n";
        }

        $this->path = preg_replace('/\/sitemap.csv\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)) {
                mkdir($this->path, 0777, true);
            }
            file_put_contents($this->path . '/sitemap.csv', $csv);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function generateJSON(): void
    {
        $json = json_encode($this->all_links, JSON_UNESCAPED_SLASHES);
        $this->path = preg_replace('/\/sitemap.json\/?$/m', "", $this->path);

        try {
            if (!is_dir($this->path)) {
                mkdir($this->path, 0777, true);
            } elseif (!is_writable($this->path)) {
                throw new Exception('Директория недоступна для записи');
            }
            file_put_contents($this->path . '/sitemap.json', $json);

        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}