<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToIntGeneratesErrors() {
    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.00');
    $this->assertEquals($err, true);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.256');
    $this->assertEquals($err, true);

    list($int, $err) = \CIDR\IPv4::addr_to_int(["passing an array"]);
    $this->assertEquals($err, true);
  }

  function testToIntReturnsCorrectIntegers() {
    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000000000000);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.255');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000011111111);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.1.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000100000000);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.1.1');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0b00000000000000000000000100000001);
  }

  function testBlockToIpAndNetmaskGeneratesErrors() {
    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask(123);
    $this->assertEquals($err, true);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('127.0.0.1');
    $this->assertEquals($err, true);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('127.0.0.1/129');
    $this->assertEquals($err, true);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('256.0.0.1/129');
    $this->assertEquals($err, true);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('127.0.0.0.1/129');
    $this->assertEquals($err, true);
  }

  function testBlockToIpAndNetmask() {
    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('192.168.1.1/24');
    $this->assertEquals($err, null);
    $this->assertEquals($ip, '192.168.1.1');
    $this->assertEquals($netmask, \CIDR\IPv4::addr_to_int('255.255.255.0')[0]);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('192.168.1.1/0');
    $this->assertEquals($err, null);
    $this->assertEquals($ip, '192.168.1.1');
    $this->assertEquals($netmask, \CIDR\IPv4::addr_to_int('0.0.0.0')[0]);

    list($ip, $netmask, $err) = \CIDR\IPv4::block_to_ip_and_netmask('192.168.1.1/1');
    $this->assertEquals($err, null);
    $this->assertEquals($ip, '192.168.1.1');
    $this->assertEquals($netmask, \CIDR\IPv4::addr_to_int('128.0.0.0')[0]);
  }

  function testMatchGeneratesErrors() {
    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/224', '192.168.1.1');
    $this->assertEquals($err, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.991/24', '192.168.1.1');
    $this->assertEquals($err, true);

    list($match, $err) = \CIDR\IPv4::match('192.168.1.1/24', '192.168.1.991');
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
  }
}
