<?php

namespace App;

use GuzzleHttp\Client;

class Notify
{
    protected $client;

    protected $access_token;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://oapi.dingtalk.com', 'verify' => false]);

        $this->access_token = CONFIG['dingtalk']['access_token'];
    }

    public function send(string $content, $title = '通知'): string
    {
        return $this->client->post('/robot/send?access_token=' . $this->access_token, [
            'json' => [
                'at' => [
                    'isAtAll' => true,
                ],
                'msgtype' => 'markdown',
                'markdown' => [
                    'title' => $title,
                    'text' => $content
                ],
            ]
        ])->getBody()->getContents();
    }

    /**
     * @param array $data
     * @param string $title
     * @return string
     */
    public function formatMultiDataToTextContentAndSend(array $data, string $title = '通知'): string
    {
        return $this->send($this->formatMultiDataToTextContent($data), $title);
    }

    /**
     * @param array $data
     * @param string $title
     * @return string
     */
    public function formatDataToTextContentAndSend(array $data, string $title = '通知'): string
    {
        return $this->send($this->formatDataToTextContent($data), $title);
    }

    /**
     * @param $data
     * @return string
     */
    public function formatMultiDataToTextContent($data): string
    {
        $text = "";
        foreach ($data as $datum) {
            $text .= $this->formatDataToTextContent($datum);
            $text .= "---\n ";
        }
        return $text;
    }

    /**
     * @param $data
     * @return string
     */
    public function formatDataToTextContent($data): string
    {
        $text = "";
        foreach ($data as $key => $value) {
            $text .= "##### {$key}: {$value}\n ";
        }
        return $text;
    }
}