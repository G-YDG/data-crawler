<?php

namespace App;

class CrawlerData
{
    // 港口中文描述
    const PORT_CODE_DESC = [
        102 => '新海港',
        101 => '秀英港',
    ];

    protected $notify;

    protected $digitalStrait;

    // 港口
    protected $portCodes;

    // 日期
    protected $clickDays;

    public function __construct()
    {
        $this->notify = new Notify();
        $this->digitalStrait = new DigitalStrait();

        $config = CONFIG['crawler']['digitalstrait'];
        $this->clickDays = $config['date'];
        $this->portCodes = $config['port_code'];
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
                    var_dump("[{$clickDay}]-[" . self::PORT_CODE_DESC[$portCode] . "]-有剩余票数!!!");
                    var_dump($ticketData);
                    var_dump('---');
                    $this->notify->formatMultiDataToTextContentAndSend($ticketData, '琼州海峡放票通知');
                } else {
                    var_dump("[{$clickDay}]-[" . self::PORT_CODE_DESC[$portCode] . "]-没有剩余票数");
                }
            }
        }
    }
}