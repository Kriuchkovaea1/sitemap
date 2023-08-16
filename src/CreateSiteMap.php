<?php

namespace Kriuchaveta\Sitemap;

use Exception;

class CreateSiteMap
{

    protected array $all_links;
    protected string $path;
    protected string $type;

    protected static array $priority = [
        0.1,
        0.5,
        1,
    ];

    protected static array $changefreqs = [
        'hourly',
        'daily',
        'weekly',];

    /**
     * @throws Exception
     */
    protected function validate($item): void
    {

        if (empty($item['loc'])) {
            throw new Exception("Ошибка при обработке тега: loc");
        }

        if (empty($item['lastmod'])) {
            throw new Exception("Ошибка при обработке тега: lastmod");
        } else {
            $d = \DateTime::createFromFormat("Y-m-d", $item['lastmod']);
            if (!($d && $d->format("Y-m-d") === $item['lastmod'])) {
                throw new Exception("Невалидный формат Даты последнего изменения файла. Допустимый формат Y-m-d. Указано: lastmod");
            }
        }

        if (empty($item['priority'])) {
            throw new Exception("Ошибка при обработке тега: priority.");
        } elseif (!in_array($item['priority'], self::$priority)) {
            throw new Exception("Невалидная Приоритетность. Допустимые значения — 0.1, 0.5 1.");

        }

        if (empty($item['changefreq'])) {
            throw new Exception("Ошибка при обработке тега: changefreq.");
        } elseif (!in_array($item['changefreq'], self::$changefreqs)) {
            throw new Exception("Невалидная Вероятная частота изменения. Допустимые значения");
        }
    }

    protected function __construct(array $all_links, string $path, string $type)
    {
        $this->all_links = $all_links;
        $this->path = $path;
        $this->type = $type;
    }

    protected function create($path, $filename, $sitemap): void
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
    protected function generateXML(): void
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
    protected function generateCSV(): void
    {
        $csv = "";

        foreach ($this->all_links as $data) {
            $csv .= $data['loc'] . ';' . $data['lastmod'] . ';' . $data['priority'] . ';' . $data['changefreq'] . "\n";
            $this->validate($data);
        }
        $this->create('sitemapCSV', './sitemap.csv', $csv);
    }

    protected function generateJSON(): void
    {
        $json = json_encode($this->all_links, JSON_UNESCAPED_SLASHES);

        $this->create('sitemapJSON', './sitemap.json', $json);

    }
}
