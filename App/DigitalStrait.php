<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class DigitalStrait
{
    protected $client;

    protected $cookie;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'http://hxwx.digitalstrait.cn', 'verify' => false]);

        $this->cookie = CONFIG['digitalstrait']['cookie'];
    }

    public function getFlightList($clickDay, $portCode = 102): array
    {
        $response = $this->client->get('/WxFerryMobile/FlightList/Index', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36 NetType/WIFI MicroMessenger/7.0.20.1781(0x6700143B) WindowsWechat(0x6309091b) XWEB/8519 Flue',
                'Referer' => 'http://hxwx.digitalstrait.cn/WxFerryMobile/Home/Index_tic?shipLineCode=HKHA&portCode=' . $portCode,
                'Cookie' => $this->cookie,
            ],
            'query' => [
                'clickDay' => $clickDay,
                'portCode' => $portCode,
                'shipLineCode' => 'HKHA',
                'ticketTypeCode' => '2',
                'energyTypeCode' => '0',
                'carTypeCode' => '12',
                'carTypeName' => '小型客车（12座以下）、轿车',
            ]
        ]);

        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getBody()->getContents());

        $data = [];

        $crawler->filter('#flightDiv > .mm')->each(function (Crawler $node, $i) use (&$data, $clickDay) {
            $departureTime = $node->filter('.time > strong')->first()->text();
            $departurePort = $node->filter('.time > span')->first()->text();
            $flightCode = $node->filter('.shipline > div')->text();
            $arrivalTime = $node->filter('.time > strong')->last()->text();
            $arrivalPort = $node->filter('.time > span')->last()->text();

            // 尝试获取剩余票数信息，如果结构有些不同，可能需要调整选择器
            $remainingTicketsText = $node->filter('.shiplinebtn > .nn')->text();
            // 假设剩余票数信息格式为 "剩 xx 张", 我们需要从这个字符串中提取数字
            preg_match('/剩 (\d+) 张/', $remainingTicketsText, $matches);
            $remainingTickets = $matches[1] ?? '未知'; // 如果找不到匹配项，则标记为未知

            $data[] = [
                '日期' => $clickDay,
                '航班' => $i,
                '开航时间' => $departureTime,
                '发出港' => $departurePort,
                '航班代号' => $flightCode,
                '预计到达时间' => $arrivalTime,
                '到达港' => $arrivalPort,
                '剩余票数' => $remainingTickets,
                '检测时间' => date('Y-m-d H:i:s')
            ];
        });

        return $data;
    }
}