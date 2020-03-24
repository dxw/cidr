<?php

namespace Dxw\CIDR;

class IPAddress
{
    /** @var callable */
    public static $IPv6Address = [\Dxw\CIDR\IPv6Address::class, 'Make'];
    /** @var callable */
    public static $IPv4Address = [\Dxw\CIDR\IPv4Address::class, 'Make'];

    public static function Make(string $address): \Dxw\Result\Result
    {
        $result = call_user_func(self::$IPv6Address, $address);
        if (!$result->isErr()) {
            return $result;
        }

        $result = call_user_func(self::$IPv4Address, $address);
        if (!$result->isErr()) {
            return $result;
        }

        return \Dxw\Result\Result::err('could not parse address as IPv6 or IPv4');
    }
}
