# cidr

PHP CIDR library

## Installation

    composer require dxw/cidr=dev-master

## API

Uses multiple return values. If $err is null then the operation succeeded. If $err is not null then you've probably done something wrong.

### list($match, $err) = \CIDR\IP::match($haystack, $needle)

Example:

    list($match, $err) = \CIDR\IP::match('192.168.1.1/24', '192.168.1.1');
    # $match => true, $err => null

    list($match, $err) = \CIDR\IP::match('::1/24', '::1');
    # $match => true, $err => null

    list($match, $err) = \CIDR\IP::match('192.168.1.1/24', '::1');
    # $match => false, $err => null

    list($match, $err) = \CIDR\IP::match('192.168.1.1/999', '::1');
    # $err => not null
