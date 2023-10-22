<?php

class Error403
{
    public $state;

    public function __construct()
    {
        $this->state = '403 info:' . PHP_EOL;
    }

    public function checkIndex($serverName, $domainDir) // Проверка наличия индексного файла
    {
        $cmd = "cd " . $domainDir . " && ls -a | grep -ix index.php";
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            $this->state .= 'index.php ОК' . PHP_EOL;
        } else $this->state .= 'Отсутствует index.php' . PHP_EOL;
    }
    
    public function checkHtaccess($serverName, $domainDir) // Проверка правил deny в .htaccess
    { 
        $cmd = "ls -a " . $domainDir . " | grep -ix .htaccess";
        $ssh = new Ssh($serverName, $cmd);
        if ($ssh->result) {
            $this->state .= 'Присутствует .htaccess' . PHP_EOL;
            $cmd = "grep -i deny " . $domainDir . ".htaccess";
            $ssh = new Ssh($serverName, $cmd);
            if ($ssh->result) {
                $this->state .= 'Присутствуют правила deny:' . PHP_EOL . $ssh->result;
            } else $this->state .= 'Отсутствуют правила deny' . PHP_EOL; 
        } else $this->state .= 'Отсутствует .htaccess' . PHP_EOL;
    }

    public function checkCharter($serverName, $userName, $domainDir) // Проверка прав на файлы и каталоги
    {
        $cmd = "cd " . $domainDir . " && ls -Rl | awk '{print $1 $9}'";
        $ssh = new Ssh($serverName, $cmd);
        $arr = explode(PHP_EOL, $ssh->result);
        foreach ($arr as $key => $val) {
            if (strlen($val) > 5) {
                if ($val[0] == 'd') { // Если папка
                    if (substr($val, 1, 9) != 'rwxr-xr-x' && substr($val, 1, 9) != 'rwxrwxrwx') {
                        $this->state .= 'Недостаточно прав для директории ' . substr($val, 10) . " " . substr($val, 1, 9) . PHP_EOL;
                    }
                } else if (!preg_match('/\./', $val[0])) {
                    if (substr($val, 1, 9) != 'rw-r--r--' && substr($val, 1, 9) != 'rwxrwxrwx') {
                        $this->state .= 'Недостаточно прав для файла ' . substr($val, 10) . " " . substr($val, 1, 9) . PHP_EOL;
                    } 
                } 
            }
        }
    }
}
