<?php

class Error500
{
    public $serverName;
    public $domainName;
    public $userDir;
    public $domainDir;
    public $state;

    public function __construct($serverName, $domainName, $userName, $userDir, $domainDir)
    {
        $this->state = '500 info:' . PHP_EOL;
        $this->$serverName = $serverName;
        $this->$domainName = $domainName;
        $this->$userDir = $userDir;
        $this->$domainDir = $domainDir;

        $this->checkHtaccess($serverName, $domainName, $userDir, $domainDir);
    }

    public function checkHtaccess($serverName, $domainName, $userDir) // Проверка файла .htaccess
    {
        $cmd = "sudo grep -i 'Invalid command' " . $userDir . "logs/" . $domainName . ".error.log";
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            preg_match("/\s'(.*)',/", $ssh->result, $arr);
            $this->state .= 'Ошибка .htaccess в строке - ' . $arr[0] . PHP_EOL;
        } else $this->state .= '.htaccess OK' . PHP_EOL;
    }

}
