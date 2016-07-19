<?php

class IPv4Test extends PHPUnit_Framework_TestCase
{
    public function testToIntGeneratesErrors()
    {
        $matrix = [
            ['0.0.00', 'ip2long returned error'],
            ['0.0.0.256', 'ip2long returned error'],
            [['passing an array'], 'not a string'],
        ];

        foreach ($matrix as $row) {
            $addr = $row[0];
            $msg = $row[1];

            $result = \CIDR\IPv4::addrToInt($addr);
            $this->assertTrue($result->isErr());
            $this->assertEquals($msg, $result->getErr());
        }
    }

    public function testToIntReturnsCorrectIntegers()
    {
        $matrix = [
            ['0.0.0.0',   0b00000000000000000000000000000000],
            ['0.0.0.255', 0b00000000000000000000000011111111],
            ['0.0.1.0',   0b00000000000000000000000100000000],
            ['0.0.1.1',   0b00000000000000000000000100000001],
        ];

        foreach ($matrix as $row) {
            $result = \CIDR\IPv4::addrToInt($row[0]);
            $this->assertFalse($result->isErr());
            $this->assertEquals($row[1], $result->unwrap());
        }
    }

    public function testNetmaskGeneratesErrors()
    {
        $matrix = [
            [129, '$i out of range'],
            ['123', '$i not of type int'],
            ['abc', '$i not of type int'],
        ];

        foreach ($matrix as $row) {
            $netmask = $row[0];
            $msg = $row[1];

            $result = \CIDR\IPv4::netmask($netmask);
            $this->assertTrue($result->isErr());
            $this->assertEquals($msg, $result->getErr());
        }
    }

    public function testNetmask()
    {
        $matrix = [
            [24, '255.255.255.0'],
            [0, '0.0.0.0'],
            [1, '128.0.0.0'],
        ];

        foreach ($matrix as $row) {
            $result = \CIDR\IPv4::netmask($row[0]);
            $this->assertFalse($result->isErr());
            $this->assertEquals(\CIDR\IPv4::addrToInt($row[1])->unwrap(), $result->unwrap());
        }
    }

    public function testMatchGeneratesErrors()
    {
        $matrix = [
            ['192.168.1.1/224',  '192.168.1.1', 'netmask portion of $haystack invalid: $i out of range'],
            ['192.168.1.991/24', '192.168.1.1', 'address portion of $haystack invalid: ip2long returned error'],
            ['192.168.1.1/24',   '192.168.1.991', '$needle invalid: ip2long returned error'],
            ['192.168.1.1/abc',  '192.168.1.1', '$haystack contains invalid netmask'],
        ];

        foreach ($matrix as $row) {
            $haystack = $row[0];
            $needle = $row[1];
            $msg = $row[2];
            $result = \CIDR\IPv4::match($haystack, $needle);

            $this->assertTrue($result->isErr());
            $this->assertEquals($msg, $result->getErr());
        }
    }

    public function testMatch()
    {
        $matrix = [
            ['192.168.1.1/24', '192.168.1.1',   true],
            ['192.168.1.1/24', '192.168.1.255', true],
            ['192.168.1.1/0',  '127.0.0.1',     true],
            ['192.168.1.1/24', '192.168.2.1',   false],
            ['192.168.1.1',    '192.168.2.1',   false],
            ['192.168.1.1',    '192.168.1.1',   true],
        ];

        foreach ($matrix as $row) {
            $result = \CIDR\IPv4::match($row[0], $row[1]);
            $this->assertFalse($result->isErr());
            $this->assertEquals($row[2], $result->unwrap());
        }
    }
}
