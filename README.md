# cidr

PHP library for matching an IP address to a CIDR range.

Supports IPv4 and IPv6.

## Installation

    composer require dxw/cidr

## Usage

To simply match two addresses:

    $result = \Dxw\CIDR\IP::contains('2001:db8:123::/64', '2001:db8:123::42');
    if ($result->isErr()) {
        // handle the error
    }
    $match = $result->unwrap();

    if ($match) {
        echo "The addresses match!\n";
    } else {
        echo "The addresses don't match.\n";
    }

## Notes

IPv4-compatible IPv6 addresses and IPv4-mapped IPv6 addresses are partially supported.

An address of the form `::127.0.0.1` or `::ffff:127.0.0.1` will be parsed. But only if they fall within `::/96` or `::ffff:0:0/96`. For example, `2001:db8::127.0.0.1` will be rejected.

But the resulting address will be treated as an IPv6 and as such it will never match an IPv4 address. For example, `127.0.0.1` will never match `::ffff:127.0.0.1` or `::127.0.0.1`.

## API

Example of testing if an IPv6 address falls within a particular IPv6 range:

    $result = \Dxw\CIDR\IPv6Range::Make('2001:db8:123::/64');
    if ($result->isErr()) {
        // handle the error
    }
    $range = $result->unwrap();

    $result = \Dxw\CIDR\IPv6Address::Make('2001:db8:123::42');
    if ($result->isErr()) {
        // handle the error
    }
    $address = $result->unwrap();

    if ($range->containsAddress($address)) {
        echo "It matches!\n";
    } else {
        echo "It doesn't match.\n";
    }

To make the example IPv4-only, replace `IPv6` with `IPv4`. To make the example version agnostic, replace `IPv6` with just `IP`.

- `IP`
    - `::contains(string $addressOrRange, string $address): \Dxw\Result\Result`
- `IPAddress`
    - `::Make(string $address): \Dxw\Result\Result`
- `IPRange`
    - `::Make(string $range): \Dxw\Result\Result`
- `IPv4Address`
    - `::Make(string $address): \Dxw\Result\Result`
    - `->__toString(): string`
    - `->getBinary(): \phpseclib\Math\BigInteger`
- `IPv6Address`
    - `::Make(string $address): \Dxw\Result\Result`
    - `->__toString(): string`
    - `->getBinary(): \phpseclib\Math\BigInteger`
- `IPv4Block`
    - `::Make(int $value): \Dxw\Result\Result`
    - `->getValue(): int`
    - `->getNetmask(): \phpseclib\Math\BigInteger`
- `IPv6Block`
    - `::Make(int $value): \Dxw\Result\Result`
    - `->getValue(): int`
    - `->getNetmask(): \phpseclib\Math\BigInteger`
- `IPv4Range`
    - `::Make(string $range): \Dxw\Result\Result`
    - `->getAddress(): \Dxw\CIDR\IPv4Address`
    - `->getBlock(): \Dxw\CIDR\IPv4Block`
    - `->containsAddress(\Dxw\CIDR\AddressBase $address): bool`
- `IPv6Range`
    - `::Make(string $range): \Dxw\Result\Result`
    - `->getAddress(): \Dxw\CIDR\IPv6Address`
    - `->getBlock(): \Dxw\CIDR\IPv6Block`
    - `->containsAddress(\Dxw\CIDR\AddressBase $address): bool`
