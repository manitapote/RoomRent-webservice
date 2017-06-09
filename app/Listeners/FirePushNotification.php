<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FirePushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PushNotification  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        $key    = env('FCM_SERVER_KEY');
        $fields = array(
            'registration_ids' => $event->tokens,
            'priority' => 'high',
            'data' => $event->data);
        $headers = array(
            'Authorization: key='. $key,
            'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) { 
            return false;
        }
        curl_close($ch);

        dd($result); 
    }
}
