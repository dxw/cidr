<?php

namespace CIDR;

class IPv4
{
    public static function addrToInt($addr)
    {
        if (!is_string($addr)) {
            return \Result\Result::err('');
        }

        $int = ip2long($addr);

        if ($int === false) {
            return \Result\Result::err('');
        }

        return \Result\Result::ok($int);
    }

    public static function netmask($i)
    {
        if (!is_int($i)) {
            return \Result\Result::err('');
        }

        if ($i < 0 || $i > 32) {
            return \Result\Result::err('');
        }

        $netmask = pow(2, $i) - 1 << (32 - $i);

        return \Result\Result::ok($netmask);
    }

    public static function match($haystack, $needle)
    {
        $a = explode('/', $haystack);

        $haystack_addr = $a[0];
        $haystack_netmask = count($a) > 1 ? $a[1] : null;

        $result = self::addrToInt($haystack_addr);
        if ($result->isErr()) {
            return $result;
        }
        $_haystack_addr = $result->unwrap();

        $result = self::addrToInt($needle);
        if ($result->isErr()) {
            return $result;
        }
        $_needle = $result->unwrap();

        if ($haystack_netmask === null) {
            return \Result\Result::ok($_haystack_addr === $_needle);
        } else {

            // Make sure string is valid int
            $haystack_netmask_i = (int) $haystack_netmask;
            if ($haystack_netmask !== (string) $haystack_netmask_i) {
                return \Result\Result::err('');
            }

            $result = self::netmask($haystack_netmask_i);
            if ($result->isErr()) {
                return $result;
            }
            $_haystack_netmask = $result->unwrap();

            $haystack_masked = $_haystack_addr & $_haystack_netmask;
            $needle_masked = $_needle & $_haystack_netmask;

            $match = $haystack_masked === $needle_masked;

            return \Result\Result::ok($match);
        }
    }
}
