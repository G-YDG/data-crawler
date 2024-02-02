<?php

namespace App;

use GuzzleHttp\Client;

class YueHai
{
    protected $client;

    protected $config;

    public function __construct()
    {
        $this->config = CONFIG['yue_hai'];
        $this->client = new Client(['base_uri' => $this->config['base_uri'] ?? 'https://wx.17u.cn', 'verify' => false]);
    }

    public function queryVoyages($clickDay)
    {
        $response = $this->client->post('/shipapi/ShipVoyageApi/QueryVoyages', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36 MicroMessenger/7.0.20.1781(0x6700143B) NetType/WIFI MiniProgramEnv/Windows WindowsWechat/WMPF WindowsWechat(0x63090926) XWEB/8555',
                'Referer' => 'https://servicewechat.com/wx34322f29c65fcd6b/49/page-frame.html',
                'xweb_xhr' => 1,
            ],
            'json' => [
                "SupplierId" => 0,
                "DepartPort" => "海口南港",
                "ArrivePort" => "徐闻北港",
                "DepartDate" => $clickDay,
                "LineId" => "1647",
                "IsRoundTrip" => 0,
                "IsReturn" => 0,
                "DepartCity" => "",
                "ArriveCity" => "",
                "IsShield" => 0,
                "DepartPorts" => [],
                "ArrivePorts" => [],
                "DepartTimes" => [],
                "ShipNames" => [],
                "LineIds" => [],
                "ChannelProject" => "yhtApplet",
                "VehicleType" => 0,
                "Channel" => "yhtApplet"
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}