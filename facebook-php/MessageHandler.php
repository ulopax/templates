<?php

require __DIR__.'/vendor/autoload.php';

use Tgallice\FBMessenger\Attachment\Image;
use Tgallice\FBMessenger\Messenger;
use Tgallice\FBMessenger\Message\Message;

class MessageHandler
{
    // more examples: see https://github.com/tgallice/fb-messenger-sdk

    /**
     * Receiving a text message
     * You can send your response here.
     *
     * @param $cmd the text message the user has sent or
     * the payload of a postback button the user has clicked
     * @param $user the user, who has send the message
     */
    public static function processMessage($cmd, $user)
    {
        $messenger = new Messenger(getenv('FACEBOOK_TOKEN'));
        $message = new Message($user->chat, $cmd);
        $messenger->sendMessage($message);
    }

    /**
     * Receiving an image
     * You can send your response here.
     *
     * @param $cmd the image or sticker the user has sent
     * @param $user the user, who has send the message
     */
    public static function processImage($imageUrl, $user)
    {
        $messenger = new Messenger(getenv('FACEBOOK_TOKEN'));

        $image = new Image('https://rainbowgram.files.wordpress.com/2014/12/b542b-10852747_1518935621724535_1634917026_n.jpg');
        $message = new Message($user->chat, $image);
        $messenger->sendMessage($message);
    }

    public static function processLocation($lat, $lon, $user)
    {
        $messenger = new Messenger(getenv('FACEBOOK_TOKEN'));

        $location = get_location($lat, $lon);
        logger($location);
        $postcode = $location['address']['postcode'];
        $country = $location['address']['country'];
        $city = $location['address']['city'];

        $message = new Message($user->chat, "You are in $city, $postcode in $country");
        $messenger->sendMessage($message);
    }
}
