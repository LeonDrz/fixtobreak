<?php

class Dns
{
    public $domainName;
    public $dnsName;
    public $nsHost;
    public $nsIp;

    public function __construct($domainName)
    {
        $this->domainName = $domainName;
        $this->getDnsName($domainName);
    }

    public function getDnsName($domainName)
    {
        $ns = dns_get_record($domainName, DNS_NS); 
        foreach ($ns as $nss) {
            $this->dnsName .= $nss['target'] . ' ';
        }
    }

    public function getMaster()
    {
        if (preg_match('/ns1.hosting.reg.ru/', $this->dnsName)) {
            $cmd = 'sudo get_supermaster.sh ' . $this->domainName; 
            $ssh = new Ssh('masterns1.hosting.reg.ru', $cmd);
            return $ssh->result;
        }
        
        if (preg_match('/ns5.hosting.reg.ru/', $this->dnsName)) {
            $nsHost = 'ns5.hosting.reg.ru';
            $cmd = 'sudo domainctl info ' . $this->domainName; 
            $ssh = new Ssh($nsHost, $cmd);
            return $ssh->result;
        }
        
        if (preg_match('/ns1.reg.ru/', $this->ns)) {
            $this->nsMaster = 'Бесплатные NS' . PHP_EOL;
        } else $this->nsMaster = 'Сторонние NS' . PHP_EOL;
    }
}
