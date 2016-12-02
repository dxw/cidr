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

- `IP`
    - `::contains(string $addressOrRange, string $address): \Dxw\Result\Result`
- `IPAddress`
    - `::Make(string $address): \Dxw\Result\Result`
- `IPRange`
    - `::Make(string $range): \Dxw\Result\Result`
- `IPv4Address`
    - `::Make(string $address): \Dxw\Result\Result`
    - `->__toString(): string`
    - `->getBinary(): \GMP`
- `IPv6Address`
    - `::Make(string $address): \Dxw\Result\Result`
    - `->__toString(): string`
    - `->getBinary(): \GMP`
- `IPv4Block`
    - `::Make(int $value)`
    - `->getValue(): int`
    - `->getNetmask(): \GMP`
- `IPv6Block`
    - `::Make(int $value)`
    - `->getValue(): int`
    - `->getNetmask(): \GMP`
- `IPv4Range`
    - `::Make(string $range): \Dxw\Result\Result`
    - `getAddress(): \Dxw\CIDR\IPv4Address`
    - `getBlock(): \Dxw\CIDR\IPv4Block`
    - `containsAddress(\Dxw\CIDR\IPv4Address $address): bool`
- `IPv6Range`
    - `::Make(string $range): \Dxw\Result\Result`
    - `getAddress(): \Dxw\CIDR\IPv6Address`
    - `getBlock(): \Dxw\CIDR\IPv6Block`
    - `containsAddress(\Dxw\CIDR\IPv6Address $address): bool`
