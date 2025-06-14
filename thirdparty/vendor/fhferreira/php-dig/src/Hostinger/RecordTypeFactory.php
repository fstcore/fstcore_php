<?php

namespace Hostinger;

use Hostinger\RecordType\RecordType;

class RecordTypeFactory
{
    private $dnsTypes = [
        DNS_ALL   => 'ALL',
        DNS_MX    => 'MX',
        DNS_CNAME => 'CNAME',
        DNS_NS    => 'NS',
        DNS_A     => 'A',
        DNS_AAAA  => 'AAAA',
        DNS_PTR  => 'PTR',
        DNS_SRV  => 'SRV',
        DNS_SOA  => 'SOA',
        DNS_TXT  => 'TXT',
        /*DNS_CAA  => 'CAA',
        DNS_DS  => 'DS',
        DNS_KEY  => 'DNSKEY'*/
    ];

    /**
     * @param int $dnsType
     * @return RecordType
     */
    public function make($dnsType)
    {
        $class = '\\Hostinger\\RecordType\\' . ucfirst(strtolower($this->convertDnsTypeToString($dnsType)));
        if (!class_exists($class)) {
            return null;
        }
        return new $class();
    }

    /**
     * @param int $dnsType
     * @return string
     */
    public function convertDnsTypeToString($dnsType)
    {
        return $this->dnsTypes[$dnsType];
    }

}
