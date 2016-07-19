<?php

namespace Dxw\CIDR;

class IPv4Range
{
    private $address;
    private $block;

    public static function Make(string $range): \Dxw\Result\Result
    {
        if (strpos($range, '/') === false) {
            $address = \Dxw\CIDR\IPv4Address::Make($range);
            $block = \Dxw\CIDR\IPv4Block::Make(32);
        } else {
            $split = explode('/', $range);

            $address = \Dxw\CIDR\IPv4Address::Make($split[0]);
            $block = \Dxw\CIDR\IPv4Block::Make($split[1]);
        }

        return \Dxw\Result\Result::ok(new self($address->unwrap(), $block->unwrap()));
    }

    private function __construct(\Dxw\CIDR\IPv4Address $address, \Dxw\CIDR\IPv4Block $block)
    {
        $this->address = $address;
        $this->block = $block;
    }

    public function getAddress(): \Dxw\CIDR\IPv4Address
    {
        return $this->address;
    }

    public function getBlock(): \Dxw\CIDR\IPv4Block
    {
        return $this->block;
    }

    public function containsAddress(\Dxw\CIDR\IPv4Address $address): bool
    {
        $thisAddress = $this->getAddress()->getBinary();
        $netmask = $this->getBlock()->getNetmask();
        $otherAddress = $address->getBinary();

        $thisAddressMasked = $this->bitwiseAnd($thisAddress, $netmask);
        $otherAddressMasked = $this->bitwiseAnd($otherAddress, $netmask);

        return $thisAddressMasked === $otherAddressMasked;
    }

    private function bitwiseAnd(string $a, string $b): string
    {
        $aArray = unpack('N*', $a);
        $bArray = unpack('N*', $b);

        if (count($aArray) !== count($bArray)) {
            trigger_error(E_USER_FATAL, 'oh no');
        }

        $cArray = [];

        for ($i = 1 ; $i <= count($aArray); $i++) {
            $cArray[] = $aArray[$i] & $bArray[$i];
        }

        return pack('N*', $cArray[0]);
    }
}
