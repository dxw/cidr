<?php

namespace Dxw\CIDR;

class IPv4Address
{
    private $address;

    public static function Make(string $address): \Dxw\Result\Result
    {
        return \Dxw\Result\Result::ok(new self($address));
    }

    private function __construct(string $address)
    {
        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->address;
    }

    public function getBinary(): \GMP
    {
        return $this->inAddrToGmp(inet_pton($this->address));
    }

    private function inAddrToGmp(string $in_addr): \GMP
    {
        $unpacked = unpack('a4', $in_addr);
        $unpacked = str_split($unpacked[1]);
        $binary = '';
        foreach ($unpacked as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return gmp_init($binary, 2);
    }
}
