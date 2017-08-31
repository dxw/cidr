<?php

namespace Dxw\CIDR;

class IP
{
    // This makes everything easy to test
    /** @var array */
    public static $IPAddress = [\Dxw\CIDR\IPAddress::class, 'Make'];
    /** @var array */
    public static $IPRange = [\Dxw\CIDR\IPRange::class, 'Make'];

    public static function contains(string $range, string $address): \Dxw\Result\Result
    {
        $result = call_user_func(self::$IPAddress, $address);
        if ($result->isErr()) {
            return $result;
        }
        $_address = $result->unwrap();

        $result = call_user_func(self::$IPRange, $range);
        if ($result->isErr()) {
            return $result;
        }
        $_range = $result->unwrap();

        return \Dxw\Result\Result::ok($_range->containsAddress($_address));
    }
}
