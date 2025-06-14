<?php

namespace Hostinger;

use Hostinger\RecordType\RecordType;

class ExecuteDigCommand
{
    private $timeout = 2;

    public function setTimeout($value)
    {
        $this->timeout = $value;
    }

    public function execute($domain, $resolver, RecordType $recordType)
    {
        $lines   = [];
        $dnsType = strtoupper($recordType->getType());
        $command = 'dig @'.$resolver.' +noall +answer +time=' . escapeshellarg($this->timeout) . ' ' . escapeshellarg($dnsType) . ' ' . escapeshellarg($domain);
        exec($command, $lines);
        if (empty($lines)) {
            return [];
        }
        return $recordType->transform($lines);
    }
}
