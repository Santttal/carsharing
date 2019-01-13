<?php

namespace App\Lib;

use GuzzleHttp\Client;
use Longman\TelegramBot\Telegram as TelegramBot;

class Telegram
{
    /** @var \TelegramBot\Api\BotApi */
    private $bot;

    private $proxy = [
        'https://83.233.162.68:8080',
        'HTTPS://92.244.220.28:41766',
        'SOCKS5://90.231.72.5:9050',
        'HTTPS://79.138.99.254:8080',
        'HTTPS://80.252.173.82:41766',
    ];

    public function __construct()
    {
        $this->bot = new TelegramBot(env('TELEGRAM_API_KEY'));
    }

    public function sendMessage($text)
    {
        $data = [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $text,
        ];

        $sent = false;
        $proxyIndex = 0;
        while (!$sent) {
            if (($proxyIndex + 1 === count($this->proxy))) {
                break;
            }
            try {
                \Longman\TelegramBot\Request::setClient($this->createClient($this->proxy[$proxyIndex]));
                \Longman\TelegramBot\Request::sendMessage($data);
                $sent = true;
            } catch (\Exception $e) {
                $proxyIndex++;
            }
        }
    }

    private function createClient($proxy)
    {
        return new Client([
            'base_uri' => 'https://api.telegram.org',
            'proxy'    => $proxy,
            'verify'   => false,
            'timeout' =>  10,
        ]);
    }
}
