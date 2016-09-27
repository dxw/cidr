<?php

namespace Dxw\CIDR;

class IPv6Range
{
    private $address;
    private $block;

    public static function Make(string $range): \Dxw\Result\Result
    {
        if (strpos($range, '/') === false) {
            $address = \Dxw\CIDR\IPv6Address::Make($range);
            $block = \Dxw\CIDR\IPv6Block::Make(128);
        } else {
            $split = explode('/', $range);

            $address = \Dxw\CIDR\IPv6Address::Make($split[0]);
            $block = \Dxw\CIDR\IPv6Block::Make($split[1]);
        }

        return \Dxw\Result\Result::ok(new self($address->unwrap(), $block->unwrap()));
    }

    private function __construct(\Dxw\CIDR\IPv6Address $address, \Dxw\CIDR\IPv6Block $block)
    {
        $this->address = $address;
        $this->block = $block;
    }

    public function getAddress(): \Dxw\CIDR\IPv6Address
    {
        return $this->address;
    }

    public function getBlock(): \Dxw\CIDR\IPv6Block
    {
        return $this->block;
    }

    private function inAddrToGmp(string $in_addr): \GMP
    {
        $unpacked = unpack('a16', $in_addr);
        $unpacked = str_split($unpacked[1]);
        $binary = '';
        foreach ($unpacked as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return gmp_init($binary, 2);
    }

    public function containsAddress(\Dxw\CIDR\IPv6Address $address): bool
    {
        $thisAddress = $this->getAddress()->getBinary();
        $netmask = $this->getBlock()->getNetmask();
        $otherAddress = $address->getBinary();

        $thisAddressMasked = $this->bitwiseAnd($this->inAddrToGmp($thisAddress), $netmask);
        $otherAddressMasked = $this->bitwiseAnd($this->inAddrToGmp($otherAddress), $netmask);

        return gmp_cmp($thisAddressMasked, $otherAddressMasked) === 0;
    }

    private function bitwiseAnd(\GMP $a, \GMP $b): \GMP
    {
        return gmp_and($a, $b);
    }
}
