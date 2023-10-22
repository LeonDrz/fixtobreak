<?php

class Ssh
{
    public $host;
    public $cmd;
    public $result;

    public function __construct($host, $cmd)
    {
        $this->host = $host;
        $this->cmd = $cmd;
        $this->connect($host, $cmd);
    }

    public function connect($host, $cmd)
    {
        //TODO Дописать определение имени пользователя и вынести в отдельный конфиг
        $connection = ssh2_connect($host, $PORT); // $PORT необходимо указать
        $publicKey = "$PATH"; // Путь до ключей SSH необходимо указать
        $privateKey = "$PATH";

        ssh2_auth_pubkey_file($connection, $USERNAME, $publicKey, $privateKey); // $USERNAME необходимо указать
        $stream = ssh2_exec($connection, $cmd);
        stream_set_blocking($stream, true);
        $this->result = stream_get_contents($stream);
    }
}
