<?php
namespace SM;

use Exception;

trait SiteMapValidation
{
    private static array $priority = [
        0.1,
        0.5,
        1,
    ];

    private static array $changefreqs = [
        'hourly',
        'daily',
        'weekly',
    ];

    /**
     * @throws Exception
     */
    public function validate($item): void
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
        } elseif (!in_array($item['changefreq'], self::$priority)) {
            var_dump($item['priority']);
            throw new Exception("Невалидная Приоритетность. Допустимые значения — 0.1, 0.5 1.");

        }

        if (empty($item['changefreq'])) {
            throw new Exception("Ошибка при обработке тега: changefreq.");
        } elseif (!in_array($item['changefreq'], self::$changefreqs)) {
            throw new Exception("Невалидная Вероятная частота изменения. Допустимые значения");
        }
    }
}
