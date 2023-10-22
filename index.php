<?php

require_once "autoload.php";

$domainName = $argv[1];
if (!isset($domainName)) {
    echo 'Input domain name' . PHP_EOL;
    die;
}

$domain = new Domain($domainName);
$serverName = $domain->serverName;
$server = new Server($serverName);
$serverType = $server->type;
$dns = new Dns($domain->domainName);

# Проверки без подключения к серверу
echo 'IP: ' . $domain->ip . PHP_EOL;
echo 'PTR: ' . $serverName . PHP_EOL;
echo 'Code: ' . $domain->code . PHP_EOL;
echo 'DNS: ' . $dns->dnsName . PHP_EOL;
echo 'Server: ' . $serverType . PHP_EOL;

# Проверки с подключением к серверу
if ($serverType == 'CloudVPS') die; // Если CloudVPS, то не подключаемся
if (isset($argv[2])) {
    if ($argv[2] == '-b') { // Информация о бэкап сервере
        $user = new User($domainName, $serverName, $serverType);
        $userName = $user->name;
        $backup = new Backup($serverName, $serverType, $userName, $domainName);
        echo 'Backup: ' . $backup->backupName . PHP_EOL;
        echo 'Backup user dir: ' . $backup->backupDir . PHP_EOL;
        echo 'Количество файлов: ' . var_dump($backup->backupFiles) . PHP_EOL;
    }

    if ($argv[2] == '-u') { // Информация о пользователе
        $user = new User($domainName, $serverName, $serverType);
        echo 'User: ' . $user->name . PHP_EOL;
        echo 'User dir: ' . $user->userDir . PHP_EOL;
        echo 'Domain dir: ' . $user->domainDir . PHP_EOL;
        echo 'UserCtl: ' . $user->state . PHP_EOL;
        echo 'FW: ' . $user->fw . PHP_EOL;
    }

    if ($argv[2] == '-d') { // Информация о мастер-сервере DNS
        echo 'DNS-master: ' . $dns->getMaster() . PHP_EOL;
    }

    if ($argv[2] == '-403') {
        $error403 = new Error403();
        $error403->checkIndex($serverName, $domainDir);
        $error403->checkHtaccess($serverName, $domainDir);
        $error403->checkCharter($serverName, $userName, $domainDir);
        echo $error403->state;
    }
}
