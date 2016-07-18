# cidr

PHP CIDR library (IPv4-only at the moment)

## Installation

    composer require dxw/cidr

## API

Uses result values. Results accept `->isErr()` to get if the result represents an error or not, and `->unwrap()` to get the value you want. Calling `->unwrap()` on a result value where `->isErr()` returns true generates a RuntimeException.

Example:

    $result = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.1');
    if ($result->isErr()) {
        // handle the error
    }
    $match = $result->unwrap();
    // $match is a bool

### $result = \CIDR\IPv4::addrToInt($addr)

Internal function.

`$addr` is an IPv4 address as a string. `$result->unwrap()` is the same address represented as an integer.

### $result = \CIDR\IPv4::netmask($i)

Internal function.

`$i` is the number after the "/" in a CIDR range. `$result->unwrap()` is the netmask as an integer.

### $result = \CIDR\IPv4::match($haystack, $needle)

Example:

    $result = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.1');

In this case 192.168.1.1 is within 192.168.1.1/24 so `$result->unwrap()` is true.
