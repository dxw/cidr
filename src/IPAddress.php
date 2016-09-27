<?php

namespace Dxw\CIDR;

class IPAddress
{
    private $address;
    protected static $unpackSize;

    public static function Make(string $address): \Dxw\Result\Result
    {
        return \Dxw\Result\Result::ok(new static($address));
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
        $unpacked = unpack('a'.static::$unpackSize, $in_addr);
        $unpacked = str_split($unpacked[1]);
        $binary = '';
        foreach ($unpacked as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return gmp_init($binary, 2);
    }
}
