<?php

namespace App;

class CrawlerData
{
    // 港口
    protected $portCodes = [
        102, // 新海港
        101, // 秀英港
    ];

    // 港口中文描述
    protected $portCodeDesc = [
        102 => '新海港',
        101 => '秀英港',
    ];

    # 日期
    protected $clickDays = [
        '2024-2-4',
        '2024-2-5',
        '2024-2-6',
        '2024-2-7',
        '2024-2-8',
    ];

    protected $notify;
    protected $digitalStrait;

    public function __construct()
    {
        $this->notify = new Notify();
        $this->digitalStrait = new DigitalStrait();
    }

    public function run()
    {
        foreach ($this->clickDays as $clickDay) {
            foreach ($this->portCodes as $portCode) {
                $ticketData = [];
                $flightList = $this->digitalStrait->getFlightList($clickDay, $portCode);
                foreach ($flightList as $flight) {
                    if (intval($flight['剩余票数']) > 0) {
                        $ticketData[] = $flight;
                    }
                }
                if (!empty($ticketData)) {
                    var_dump('---');
                    var_dump("[{$clickDay}]-[{$this->portCodeDesc[$portCode]}]-有剩余票数!!!");
                    var_dump($ticketData);
                    var_dump('---');
                    $this->notify->formatMultiDataToTextContentAndSend($ticketData, '琼州海峡放票通知');
                } else {
                    var_dump("[{$clickDay}]-[{$this->portCodeDesc[$portCode]}]-没有剩余票数");
                }
            }
        }
    }
}