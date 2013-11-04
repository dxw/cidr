<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToInt() {

    // Errors

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.00');
    $this->assertEquals($err, true);
    $this->assertEquals($int, 0);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.256');
    $this->assertEquals($err, true);
    $this->assertEquals($int, 0);

    // No errors

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 0);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.0.255');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 255);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.1.0');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 256);

    list($int, $err) = \CIDR\IPv4::addr_to_int('0.0.1.1');
    $this->assertEquals($err, null);
    $this->assertEquals($int, 257);
  }
}
