<?php


namespace VLWorks\BazisBot;

use stdClass;

class BazisClipHttpSendler
{
    private string $API_URL = 'https://api.telegram.org/';
    private string $TOKEN;
    private string $BOT_PATH;
    private string $PROJECT;

    public function __construct(string $token, $project)
    {
        $this->TOKEN = $token;
        $this->BOT_PATH = 'bot' . $this->TOKEN . '/';

        $this->PROJECT=$project;
    }

    public function sendMessage(string $text, string | int $user, bool $debug = false) : int
    {
        $method = 'sendMessage';
        $query = [
            'text' => $this->PROJECT . ' | ' . $text,
            'chat_id' => $user,
        ];

        $response = $this->send($this->preparePath($method, $query), $debug);
        if ($response->ok)
            return $response->result->message_id;
        else
            return -1;
    }

    private function preparePath(string $method, array $query): string
    {
        return $this->API_URL . $this->BOT_PATH . $method . '?' . http_build_query($query);
    }

    private function send($url, $debug): stdClass
    {
        $response = new stdClass();
        $response->ok = true;
        $response->result = null;

        $data = @file_get_contents($url);

        if ($data) {
            $response->result = json_decode($data)->result;
        }
        else {
            $response->ok = false;

            if ($debug)
                file_put_contents('error.log', date('Y-m-d-H-i-s') . ' | ' . error_get_last()['message'], FILE_APPEND);
        }

        return $response;
    }
}