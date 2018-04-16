<?php
namespace App\Core\Parsers;

use App\Core\Interfaces\CourseParserInterface;
use App\Opif;

class HtmlCourseParser implements CourseParserInterface
{
    public function parse(Opif $opif)
    {
        $url = $opif->publicDataUrl;

        try {
            $result = $this->fetchHtml($url);
        } catch (\Exception $e) {
            $result = "";
        }

        $parseResult = $this->parseHtml($result);

        return $parseResult;
    }

    protected function fetchHtml($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36',
        ]);

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if (false === $result) {
            throw new \Exception('Error from cURL [' . $errno . ']: ' . $error);
        }

        return $result;
    }

    protected function parseHtml($html)
    {
        if (!preg_match('~<table class="table-pif-cost mtX1 mbX3">(.*?)</table>~is', $html, $matches)) {
            return null;
        }

        $table = $matches[1];

        if (!preg_match('~<thead>\s+<tr>\s+<th>.*?</th>\s+<th>(.*?)</th>\s+<th>(.*?)</th>~is', $table, $matches)) {
            return null;
        }

        $prevDate = strip_tags($matches[2]);
        $currDate = strip_tags($matches[1]);

        if (!preg_match('~<tbody>\s+<tr>.*?</tr>\s+<tr>\s+<td class="table-pif-cost__label">.*?</td>\s+<td class="table-pif-cost__value">(.*?)</td>\s+<td class="table-pif-cost__value">(.*?)</td>~is', $table, $matches)) {
            return null;
        }

        $prevCourse = $matches[2];
        $currCourse = $matches[1];

        return [
            'prev' => [
                'date' => $prevDate,
                'course' => $prevCourse,
            ],
            'curr' => [
                'date' => $currDate,
                'course' => $currCourse,
            ],
        ];
    }
}
