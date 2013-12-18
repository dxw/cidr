<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToIntGeneratesErrors() {
    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.00');
    $this->assertEquals($err, true);

    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.0.256');
    $this->assertEquals($err, true);

    list($int, $err) = \CIDR\IPv4::addrToInt(["passing an array"]);
    $this->assertEquals($err, true);
  }

  function testToIntReturnsCorrectIntegers() {
    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.0.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000000000000);

    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.0.255');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000011111111);

    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.1.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000100000000);

    list($int, $err) = \CIDR\IPv4::addrToInt('0.0.1.1');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000100000001);
  }

  function testNetmaskGeneratesErrors() {
    list($netmask, $err) = \CIDR\IPv4::netmask(129);
    $this->assertEquals($err, true);

    list($netmask, $err) = \CIDR\IPv4::netmask('123');
    $this->assertEquals($err, true);

    list($netmask, $err) = \CIDR\IPv4::netmask('abc');
    $this->assertEquals($err, true);
  }

  function testNetmask() {
    list($netmask, $err) = \CIDR\IPv4::netmask(24);
    $this->assertEquals($err, null);
    $this->assertEquals($netmask, \CIDR\IPv4::addrToInt('255.255.255.0')[0]);

    list($netmask, $err) = \CIDR\IPv4::netmask(0);
    $this->assertEquals($err, null);
    $this->assertEquals($netmask, \CIDR\IPv4::addrToInt('0.0.0.0')[0]);

    list($netmask, $err) = \CIDR\IPv4::netmask(1);
    $this->assertEquals($err, null);
    $this->assertEquals($netmask, \CIDR\IPv4::addrToInt('128.0.0.0')[0]);
  }

  function testMatchGeneratesErrors() {
    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/224', '192.168.1.1');
    $this->assertEquals($err, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.991/24', '192.168.1.1');
    $this->assertEquals($err, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.991');
    $this->assertEquals($err, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/abc', '192.168.1.1');
    $this->assertEquals($err, true);
  }

  function testMatch() {
    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.1');
    $this->assertEquals($err, null);
    $this->assertEquals($match, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.255');
    $this->assertEquals($err, null);
    $this->assertEquals($match, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/0', '127.0.0.1');
    $this->assertEquals($err, null);
    $this->assertEquals($match, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.2.1');
    $this->assertEquals($err, null);
    $this->assertEquals($match, false);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1', '192.168.2.1');
    $this->assertEquals($err, null);
    $this->assertEquals($match, false);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1', '192.168.1.1');
    $this->assertEquals($err, null);
    $this->assertEquals($match, true);
  }
}
