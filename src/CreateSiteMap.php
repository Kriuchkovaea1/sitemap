<?php

namespace SM;

use Exception;


;

/*
 * composer
 * readme
 * валидация входных данных
 * нарушенная инкапсуляция
 *
 *
 *
 * */


class CreateSiteMap
{

    use SiteMapValidation;

    protected array $all_links;
    protected string $path;
    protected string $type;


    public function __construct(array $all_links, string $path, string $type)
    {
        $this->all_links = $all_links;
        $this->path = $path;
        $this->type = $type;
    }

    public function create($path, $filename, $sitemap): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        file_put_contents($path . $filename, $sitemap);
    }

    /**
     * @throws \DOMException
     * @throws Exception
     */
    function generateXML(): void
    {
        $dom = new \DomDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('urlset');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $root->setAttribute('xsi:schemaLocation', "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");

        $dom->appendChild($root);

        $sxe = simplexml_import_dom($dom);

        foreach ($this->all_links as $item) {

            $urlMain = $sxe->addChild('url');
            $urlMain->addChild('loc', htmlspecialchars($item['loc']));
            $urlMain->addChild('lastmod', $item['lastmod']);
            $urlMain->addChild('priority', $item['priority']);
            $urlMain->addChild('changefreq', $item['changefreq']);
            $this->validate($item);
        }
        $dom->appendChild($root);
        $this->path = "sitemapXML";

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
        $dom->save($this->path . './sitemap.xml');

    }

    /**
     * @throws Exception
     */
    private function generateCSV(): void
    {
        $csv = "";

        foreach ($this->all_links as $data) {
            $csv .= $data['loc'] . ';' . $data['lastmod'] . ';' . $data['priority'] . ';' . $data['changefreq'] . "\n";
            $this->validate($data);
        }
        $this->create('sitemapCSV', './sitemap.csv', $csv);
    }

    private function generateJSON(): void
    {
        $json = json_encode($this->all_links, JSON_UNESCAPED_SLASHES);

        $this->create('sitemapJSON', './sitemap.json', $json);

    }
}

$all_links = [['loc' => 'https://site.ru',
    'lastmod' => '2020-12-14',
    'priority' => 0.1,
    'changefreq' => 'hourly'],
    ['loc' => 'https://site.ru/about',
        'lastmod' => '2020-12-10',
        'priority' => 0.5,
        'changefreq' => 'daily'],];


$json = new CreateSiteMap($all_links, '/sitemap.csv', 'CSV');
try {
    $json->generateXML();
} catch
(Exception $ex) {
    echo $ex->getMessage();
}
