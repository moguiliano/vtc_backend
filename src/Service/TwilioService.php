<?php
// src/Service/TwilioService.php
namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from
    ) {}

    private function client(): Client
    {
        return new Client($this->sid, $this->token);
    }

    public function sendSms(string $to, string $body): string
    {
        $m = $this->client()->messages->create($to, [
            'from' => $this->from,
            'body' => $body,
        ]);
        return $m->sid;
    }
}
