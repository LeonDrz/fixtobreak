<?php

class Server
{
    public $name;
    public $type;
    public function __construct($serverName)
    {
        $this->name = $serverName;
        $this->getType($serverName);
    }

    function getType($name) // Определяем тип сервера
    {
        if (preg_match('/server/', $name)) {
            $this->type = 'isp';
            return;
        } if (preg_match('/vip/', $name)) {
            $this->type = 'isp';
            return;
        } else if (preg_match('/spp/', $name)) {
            $this->type = 'isp';
            return;
        } else if (preg_match('/sbx/', $name)) {
            $this->type = 'isp';
            return;
        } else if (preg_match('/scp/', $name)) {
            $this->type = 'scp';
            return;
        } else if (preg_match('/spl/', $name)) {
            $this->type = 'spl';
            return;
        } else if (preg_match('/cloudvps.regruhosting/', $name)) {
            $this->type = 'CloudVPS';
            return;
        } else if (preg_match('/wpl/', $name)) {
            $this->type = 'Windows';
            return;
        } else if (preg_match('/ovz/', $name)) {
            $this->type = 'ovz';
            return;
        } else if (!preg_match('/reg.ru/', $name)) { 
            // TODO не работает с PTR, и с доп.IP. Нужна проверка по whois
            $this->type = 'Сторонний сервер';
            return;
        }
    }
}