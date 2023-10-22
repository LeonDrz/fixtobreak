<?php

class Domain
{
    public $domainName;
    public $serverName;
    public $curl;
    public $code;
    public $ip;

    public function __construct($domainName)
    {
        $this->domainName = $domainName;
        $this->getCurl($domainName);
    }

    public function getCurl($domainName)
    {
        # Создаем запрос cURL
        $ch = curl_init($domainName);
        curl_setopt($ch, CURLOPT_NOBODY, 1); // только заголовки
        curl_exec($ch);
        $this->curl = curl_getinfo($ch);
        curl_close($ch);
        //TODO try catch некорректный адрес
        //TODO mx записи
        
        if (!$this->curl["primary_ip"]) { // Если нет IP
            echo 'Отсутствует IP-адрес' . PHP_EOL;
            die();
        }

        $this->ip = $this->curl["primary_ip"];
        $this->serverName = gethostbyaddr($this->ip);
        $this->code = $this->curl["http_code"];
        
        // Рекурсия, если редирект
        if (preg_match("/3../", $this->code)) {
            $redirectUrl = $this->curl["redirect_url"];
            echo "Перенаправлено на: " . $redirectUrl . PHP_EOL;
            $this->getCurl($redirectUrl);
        }
    }
}
