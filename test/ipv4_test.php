<?php

class IPv4Test extends PHPUnit_Framework_TestCase {
  function testToInt() {
    $this->assertEquals(\CIDR\IPv4::addr_to_int('0.0.0.0'), 0);
    $this->assertEquals(\CIDR\IPv4::addr_to_int('0.0.0.255'), 255);
    $this->assertEquals(\CIDR\IPv4::addr_to_int('0.0.1.0'), 256);
    $this->assertEquals(\CIDR\IPv4::addr_to_int('0.0.1.0'), 256);
  }
}
