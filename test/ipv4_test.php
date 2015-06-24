<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToIntGeneratesErrors() {
    $matrix = [
      '0.0.00',
      '0.0.0.256',
      ["passing an array"],
    ];

    foreach ($matrix as $addr) {
      list($int, $err) = \CIDR\IPv4::addrToInt($addr);
      $this->assertEquals($err, true);
    }
  }

  function testToIntReturnsCorrectIntegers() {
    $matrix = [
      ['0.0.0.0',   0b00000000000000000000000000000000],
      ['0.0.0.255', 0b00000000000000000000000011111111],
      ['0.0.1.0',   0b00000000000000000000000100000000],
      ['0.0.1.1',   0b00000000000000000000000100000001],
    ];

    foreach ($matrix as $row) {
      list($int, $err) = \CIDR\IPv4::addrToInt($row[0]);
      $this->assertEquals($err, null);
      $this->assertEquals($int, $row[1]);
    }
  }

  function testNetmaskGeneratesErrors() {
    $matrix = [
      129,
      '123',
      'abc',
    ];

    foreach ($matrix as $netmask) {
      list($netmask, $err) = \CIDR\IPv4::netmask($netmask);
      $this->assertEquals($err, true);
    }
  }

  function testNetmask() {
    $matrix = [
      [24, '255.255.255.0'],
      [0, '0.0.0.0'],
      [1, '128.0.0.0'],
    ];


    foreach ($matrix as $row) {
      list($netmask, $err) = \CIDR\IPv4::netmask($row[0]);
      $this->assertEquals($err, null);
      $this->assertEquals($netmask, \CIDR\IPv4::addrToInt($row[1])[0]);
    }
  }

  function testMatchGeneratesErrors() {
    $matrix = [
      ['192.168.1.1/224',  '192.168.1.1'],
      ['192.168.1.991/24', '192.168.1.1'],
      ['192.168.1.1/24',   '192.168.1.991'],
      ['192.168.1.1/abc',  '192.168.1.1'],
    ];

    foreach ($matrix as $row) {
      list($match, $err) = \CIDR\IPv4::match($row[0], $row[1]);
      $this->assertEquals($err, true);
    }
  }

  function testMatch() {
    $matrix = [
      ['192.168.1.1/24', '192.168.1.1',   true],
      ['192.168.1.1/24', '192.168.1.255', true],
      ['192.168.1.1/0',  '127.0.0.1',     true],
      ['192.168.1.1/24', '192.168.2.1',   false],
      ['192.168.1.1',    '192.168.2.1',   false],
      ['192.168.1.1',    '192.168.1.1',   true],
    ];

    foreach ($matrix as $row) {
      list($match, $err) = \CIDR\IPv4::match($row[0], $row[1]);
      $this->assertEquals($err, null);
      $this->assertEquals($match, $row[2]);
    }
  }
}
