<?php

namespace App\helpers;

class Notification{
    
    public function __construct(){

        define( 'API_ACCESS_KEY', 'AAAAzehTylQ:APA91bEYweB-KaMaO7swequpc--9iiOufnK8wP-1_qrjYUK5rveTGCQqQq1AfnqM3ZshSet0BFDEjj3cIUF-kBA7oA41kXGMbGRXBNmxXSoK_iHzBK5XjlSa0xb5yq-fKe3DwIG9Rj5n' );
    }

    public function push_notification($title, $body, $notification, $tokens,$token=null)
    {
        #prep the bundle
        $msg = array
        (
            'body' => $body,
            'title' => $title,
            'message' => $body,
            'msgcnt' => 1,
            'icon' => "https://raken.360marketingtec.com/assets/icons/cabrio.png",
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'https://raken.360marketingtec.com/assets/icons/cabrio.png',
            'smallIcon' => 'https://raken.360marketingtec.com/assets/icons/cabrio.png',
        );
        if (isset($notification->url) && !empty($notification->url)) {
            $msg['url'] = $notification->url;
        }
        if ($notification->object_id != 0) {
            $msg['object'] = $notification->object;
            $msg['object_id'] = $notification->object_id;
        }


        $fields = array
        (
            'registration_ids' => $tokens,
            // to => $token,
            'notification' => $msg,
            'data' => $msg //title , message ,  content ,object , object_id
        );
        $auth_token = ($token == null) ? API_ACCESS_KEY : $token ;

        $headers = array
        (
            'Authorization: key=' . $auth_token,
            'Content-Type: application/json'
        );

        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}