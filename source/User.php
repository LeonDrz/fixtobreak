<?php

class User
{
    public $name;
    public $userDir;
    public $domainDir;
    public $state;
    public $fw;

    public function __construct($domain, $serverName, $serverType)
    {
        $this->getName($domain, $serverName, $serverType);
        $this->getUserInfo($serverName);
        $this->getFw($domain, $serverName);
    }

    public function getName($domain, $serverName, $serverType) // Находим имя пользователя
    {   // TODO не работает с киррилическими доменами
        if ($serverType == 'isp') {
            $cmd = "grep -ir " . $domain . " /etc/nginx/vhosts/* | head -1";
            $ssh = new Ssh($serverName, $cmd);
            $ssh->connect($serverName, $cmd);
            $this->name = substr($ssh->result, 18, 8);
            $this->userDir = '/var/www/' . $this->name . '/data/';
            $this->domainDir = '/var/www/' . $this->name . '/data/www/' . $domain . '/';
            return;
        } 
        
        if ($serverType == 'spl') {
            $cmd = 'sudo grep ' . $domain . ' /etc/apch2_2/conf.d/zz010_psa_httpd.conf | grep "DocumentRoot" | sed "1!d"';
            $ssh = new Ssh($serverName, $cmd);
            $result = trim($ssh->result);
            $this->name = substr($result, 30, 8);          
            $this->userDir = substr($result, 15, 45);
            $this->domainDir = substr($result, 15, 46) . $domain;
            return;
        } 

        if ($serverType == 'scp') {
            $cmd = "sudo grep " . $domain . " /etc/apache2/conf/httpd.conf | grep DocumentRoot | sed '1!d'";
            $ssh = new Ssh($serverName, $cmd);
            $result = trim($ssh->result);
            $this->name = substr($result, 22, 8);
            $this->userDir = substr($result, 13, 30);
            $this->domainDir = substr($result, 13, 30) . $domain;
            return;
        }   
    }

    public function getUserInfo($serverName) // Выполняем userctl check
    {
        $cmd = "sudo userctl check " . $this->name; // ok
        $ssh = new Ssh($serverName, $cmd); // ok
        if (strlen($ssh->result) <= 76){
            $this->state .= 'Userctl: OK';
        } else {
            $arr = explode(':', $ssh->result);
            unset($arr[0]);
            foreach ($arr as $key => $val) {
                if (strlen($val) > 10) $this->state .= trim($val) . PHP_EOL;
            }
        }
    }

    public function getFw($domain, $serverName) // Выполняем userctl fw list
    {
        $cmd = "sudo userctl fw list " . $domain;
        $ssh = new Ssh($serverName, $cmd);
        $this->fw = $ssh->result;
    }
}
