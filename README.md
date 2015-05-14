# cidr

PHP CIDR library (IPv4-only at the moment)

## Installation

    composer require dxw/cidr=dev-master

## API

Uses multiple return values.  If $err is null then the operation succeeded. If $err is not null then you've probably done something wrong.

### list($int, $err) = \CIDR\IPv4::addrToInt($addr)

Internal function.

$addr is an IPv4 address as a string. $int is the same addresse represented as an integer.

### list($netmask, $err) = \CIDR\IPv4::netmask($i)

Internal function.

$i is the number after the "/" in a CIDR range. It returns the netmask as an integer.

### list($match, $err) = \CIDR\IPv4::match($haystack, $needle)

Example:

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.1');

In this case 192.168.1.1 is within 192.168.1.1/24 so $match is true.
