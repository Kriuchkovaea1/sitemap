Библиотека генерации карты сайта

Библиотека генерации карты сайта в различных файловых форматах: xml, csv, json
При инициализации библиотеки в скрипте передается список страниц сайта в виде массива с параметрами: адрес страницы (loc), дата изменения страницы (lastmod), приоритет парсинга (priority), периодичность обновления (changefreq).
Также при инициализации передается тип файла для генерации: xml, csv, json; и путь к файлу для сохранения.
После инициализации объект библиотеки формирует файл выбранного типа карты сайта.

Для установки:
composer require kriuchaveta/sitemap

Примеры генерируемых файлов

Список страниц сайта

$all_links = [
    [
    
        'loc' => 'https://site.ru',
        'lastmod' => '2020-12-14',
        'priority' => 1,
        'changefreq' => 'hourly'
    ],
    [
        'loc' => 'https://site.ru/about',
        'lastmod' => '2020-12-10',
        'priority' => 0.5,
        'changefreq' => 'daily'
    ],
];


XML

$sitemap = new CreateSiteMap($all_links, 'xml', './storage/xml/sitemap.xml');

try {
  $sitemap->generateXML();
} catch (Exception $ex) {
  echo $ex->getMessage();
}


CSV

$sitemap = new CreateSiteMap($all_links, './csv/sitemap.csv', 'csv');

try {
  $sitemap->generateCSV();
} catch (Exception $ex) {
  echo $ex->getMessage();
}

JSON

$sitemap = new CreateSiteMap($all_links, './json/sitemap.xml', 'json', );

try {
  $sitemap->generateJSON();
} catch (Exception $ex) {
  echo $ex->getMessage();
}

