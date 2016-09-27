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

    public function containsAddress(\Dxw\CIDR\IPv6Address $address): bool
    {
        $thisAddress = $this->getAddress()->getBinary();
        $netmask = $this->getBlock()->getNetmask();
        $otherAddress = $address->getBinary();

        $thisAddressMasked = gmp_and($thisAddress, $netmask);
        $otherAddressMasked = gmp_and($otherAddress, $netmask);

        return gmp_cmp($thisAddressMasked, $otherAddressMasked) === 0;
    }
}
