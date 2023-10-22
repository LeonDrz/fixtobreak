<?php

class Error404
{
    public $serverName;
    public $domainName;
    public $userDir;
    public $domainDir;
    public $state;

    public function __construct($serverName, $domainName, $userName, $userDir, $domainDir)
    {
        $this->state = '404 info:' . PHP_EOL;
        $this->$serverName = $serverName;
        $this->$domainName = $domainName;
        $this->$userDir = $userDir;
        $this->$domainDir = $domainDir;

        $this->checkDir($serverName, $domainName, $userDir);
        $this->checkIndex($serverName, $domainDir);
    }

    private function checkDir($serverName, $domainName, $userDir) // Проверка наличия каталога домена
    {
        $cmd = "ls -a " . $userDir . "www/ | grep -ix " . $domainName;
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            $this->state .= 'Каталог домена ОК' . PHP_EOL;
        } else {
            $this->state .= 'Отсутствует каталог домена' . PHP_EOL;
        }
    }

    public function checkIndex($serverName, $domainDir) // Проверка наличия индексного файла
    {
        $cmd = "cd " . $domainDir . " && ls -a | grep -ix index.php";
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            $this->state .= 'index.php ОК' . PHP_EOL;
        } else $this->state .= 'Отсутствует index.php' . PHP_EOL;
    }

    public function checkHtaccess($serverName, $domainDir) // Проверка наличия файла .htaccess
    { 
        $cmd = "ls -a " . $domainDir . " | grep -ix .htaccess";
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            $this->state .= 'Присутствует .htaccess' . PHP_EOL;
        } else $this->state .= 'Отсутствует .htaccess' . PHP_EOL;
    }
}
