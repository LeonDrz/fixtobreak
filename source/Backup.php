<?php

class Backup
{
    public $serverName;
    public $serverType;
    public $userName;
    public $domainName;
    public $backupName;
    public $backupDir;
    public $date;
    public $backupFiles;
    
    public function __construct($serverName, $serverType, $userName, $domainName)
    {
        $this->serverName = $serverName;
        $this->serverType = $serverType;
        $this->userName = $userName;
        $this->domainName = $domainName;
        $this->date = date('Ymd');
        $this->getBackupName();
        $this->get_backup_data($userName, $domainName);
    }

    public function getBackupName() 
    {
        $cmd = "curl -s 'http://backups.hosting.reg.ru/backup_server.php?action=get&host=" . $this->serverName . "'; echo";
        $ssh = new Ssh($this->serverName, $cmd);
        $this->backupName = trim($ssh->result);
    }

    public function get_backup_data($userName, $domainName) 
    {
        if ($this->serverType == 'isp') {
            $this->backupDir = "/backups/" . $this->serverName . "/userdata." . $this->date -1 . "/" . $userName . "/data/www/" . $domainName . "/";
            # Формируем команду для проверки 30 каталогов
            $cmd = "";
            for ($i = 1; $i < 30; $i++) {
                $date = new DateTime();
                $date->modify("-$i day");
                $backupDir = "/backups/" . $this->serverName . "/userdata." . $date->format("Ymd") . "/" . $userName . "/data/www/" . $domainName . "/";
                $cmd .= "ls " . $backupDir . " | wc -l; ";
            }
            $ssh = new Ssh($this->backupName, $cmd);
            if ($ssh->result) {
                $this->backupFiles = $ssh->result;
            }
        }

        if ($this->serverType == 'spl') {
            $this->backupDir = "/backups/" . $this->serverName . "/userdata." . $this->date -1 . "/" . $this->userName . ".plsk.regruhosting.ru/" . $domainName . "/";
            # Формируем команду для проверки 30 каталогов
            $cmd = "";
            for ($i = 1; $i < 30; $i++) {
                $date = new DateTime();
                $date->modify("-$i day");
                $backupDir = "/backups/" . $this->serverName . "/userdata." . $date->format("Ymd") . "/" . $userName . ".plsk.regruhosting.ru/" . $domainName . "/";
                $cmd .= "sudo ls " . $backupDir . " | wc -l; ";
            }
            $ssh = new Ssh($this->backupName, $cmd);
            if ($ssh->result) {
                $this->backupFiles = $ssh->result;
            }
        }

        if ($this->serverType == 'scp') {
            $this->backupDir = "/backups/" . $this->serverName . "/userdata." . $this->date -1 . "/" . $userName . "/www/" . $domainName . "/";
            # Формируем команду для проверки 30 каталогов
            $cmd = "";
            for ($i = 1; $i < 30; $i++) {
                $date = new DateTime();
                $date->modify("-$i day");
                $backupDir = "/backups/" . $this->serverName . "/userdata." . $date->format("Ymd") . "/" . $userName . "/www/" . $domainName . "/";
                $cmd .= "sudo ls " . $backupDir . " | wc -l; ";
            }
            $ssh = new Ssh($this->backupName, $cmd);
            if ($ssh->result) {
                $this->backupFiles = $ssh->result;
            }
        }
    }
}
