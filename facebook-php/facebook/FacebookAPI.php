<?php

namespace Facebook;

class FacebookAPI
{
    public function connect()
    {
        return $this->sendAPIRequest(getenv('FACEBOOK_TOKEN'), '/me/subscribed_apps', '{}');
    }

    public function sendText($user, $text)
    {
        $payload = [
            'recipient' => ['id' => $user->chat],
            'message' => ['text' => $text],
        ];

        $this->sendAPIRequest(getenv('FACEBOOK_TOKEN'), '/me/messages', $payload);
    }

    private function sendAPIRequest($token, $method, $payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.6$method?access_token=$token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result;
    }

    private function getTemplate($title) {
        $template = [
            [
                'title' => $title,
                'buttons' => [
                    $this->getButton('postback', 'en', 'english'),
                    $this->getButton('postback', 'de', 'deutsch'),
                    $this->getButton('postback', 'cancel', 'cancel'),
                ],
            ]
        ];

        return [
            'type' => 'template',
            'payload' => [
                'template_type' => 'generic',
                'elements' => $template,
            ]
        ];
    }

    private function getButton($type, $title, $payload) {
        if($type == 'postback') {
            return [
                'type' => $type,
                'title' => $title,
                'payload' => $payload,
            ];
        } else {
            return [
                'type' => $type,
                'title' => $title,
                'url' => $payload,
            ];
        }
    }
}
