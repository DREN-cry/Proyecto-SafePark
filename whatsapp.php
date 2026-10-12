<?php

function enviarWhatsApp($numero, $mensaje) {
    $token = "EAASpxqjZBPc0BRIdxFiJIFWuR4CA747qZBvMtXFPEYaXwy66Nr2WBaBLQpTdBTc2wYr5wJYml8qtOebdoEcWmibjct9gm6PgMdF6ux8aCZBlCIVJoZCDBlZA9CDYQPwfJuG2ZAbOpDjnqXbWIMGzA33az19PF0eZAOGpeDPqZALj3jxFqQu7SygsJZBecEhc2Ubsz2zD6zzWnBrGqpDZCoTiA3vl7qhKTCuwEB0VHFqdZCs11WK2uJOrhHrtZBZCVahzRV6zPOFwyTAPIMdHogtkQDgIbvawZD";          // token válido
    $phone_id = "1055556810967521";    

    $url = "https://graph.facebook.com/v19.0/$phone_id/messages";

    $data = [
        "messaging_product" => "whatsapp",
        "to" => $numero,
        "type" => "text",
        "text" => [
            "body" => $mensaje
        ]
    ];

    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

