<?php

namespace App\Helpers;

class SMS
{
    public static function customSmsSend($sender_id, $apiKey, $mobileNo, $message)
    {
        $url = "https://bulksmsbd.net/api/smsapi";

        $data = [
            "api_key" => $apiKey,
            "senderid" => $sender_id,
            "number" => $mobileNo,
            "message" => $message
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = explode(',', $response); //for decode data
        $final_data = substr($data[0], 1);
        return substr($final_data, 16, 3);
    }

    public static function remainingSms()
    {
        $url = "https://bulksmsbd.net/api/getBalanceApi";
        $api_key = env('SMS_API_KEY');
        $data = [
            "api_key" => $api_key
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $remaining_sms_balance = $response;
        $data = explode(',', $remaining_sms_balance);
        $value = is_numeric(substr($data[1], 10, 7)) ? substr($data[1], 10, 7) : 0;
        return number_format($value / 0.23);
    }
}
