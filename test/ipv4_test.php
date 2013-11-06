<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToIntGeneratesErrors() {
    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.00');
    $this->assertEquals($err, true);
    $this->assertEquals($int, 0);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.256');
    $this->assertEquals($err, true);
    $this->assertEquals($int, 0);
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
}
