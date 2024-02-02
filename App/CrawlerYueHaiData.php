<?php

namespace App;

class CrawlerYueHaiData
{
    // 港口中文描述
    const PORT_CODE_DESC = [
        102 => '新海港',
        101 => '秀英港',
    ];

    protected $notify;

    protected $yueHai;

    // 日期
    protected $clickDays;

    public function __construct()
    {
        $this->notify = new Notify();
        $this->yueHai = new YueHai();

        $config = CONFIG['crawler']['yue_hai'];
        $this->clickDays = $config['date'];
    }

    public function run()
    {
        foreach ($this->clickDays as $clickDay) {
            $voyages = [];
            $data = $this->yueHai->queryVoyages($clickDay)['Data']['Voyages'] ?? [];
            foreach ($data as $datum) {
                $isValid = false;
                foreach ($datum['ShipSeats'] as $shipSeat) {
                    if ($shipSeat['SeatName'] != '坐席' && !empty($shipSeat['TicketLeft'])) {
                        $isValid = true;
                    }
                }
                if (!$isValid) {
                    continue;
                }
                $shipSeatStr = '';
                foreach ($datum['ShipSeats'] as $shipSeat) {
                    if (!empty($shipSeatStr)) {
                        $shipSeatStr .= '、';
                    }
                    $shipSeatStr .= "{$shipSeat['SeatName']}({$shipSeat['TicketLeft']})";
                }
                $voyages[] = [
                    '航班' => $datum['ShipNo'],
                    '航班代号' => $datum['ShipCode'],
                    '航班名称' => $datum['ShipName'],
                    '航线' => $datum['LineName'],
                    '开航时间' => $datum['DepartDateTime'],
                    '到达时间' => $datum['ArriveDateTime'],
                    '是否有票' => $datum['HaveTicket'],
                    '船位信息' => $shipSeatStr,
                    '检测时间' => date('Y-m-d H:i:s'),
                ];
            }

            if (!empty($voyages)) {
                var_dump('---');
                var_dump("[{$clickDay}]-有剩余票数!!!");
                var_dump($voyages);
                var_dump('---');
                $this->notify->formatMultiDataToTextContentAndSend($voyages, '粤海铁放票通知');
            } else {
                var_dump("[{$clickDay}]-没有剩余票数");
            }
        }

    }
}