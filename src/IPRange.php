<?php

namespace Dxw\CIDR;

class IPRange
{
    public static $IPv6Range = [\Dxw\CIDR\IPv6Range::class, 'Make'];
    public static $IPv4Range = [\Dxw\CIDR\IPv4Range::class, 'Make'];

    public static function Make(string $range) : \Dxw\Result\Result
    {
        $result = call_user_func(self::$IPv6Range, $range);
        if (!$result->isErr()) {
            return $result;
        }

        $result = call_user_func(self::$IPv4Range, $range);
        if (!$result->isErr()) {
            return $result;
        }

        return \Dxw\Result\Result::err('could not parse range as IPv6 or IPv4');
    }
}
