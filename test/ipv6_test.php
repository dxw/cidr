<?php

function bchexdec($hex) {
  $dec = 0;
  $len = strlen($hex);
  for ($i = 1; $i <= $len; $i++) {
    $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
  }
  return $dec;
}

class IPv6Test extends PHPUnit_Framework_TestCase {
  function testToIntGeneratesErrors() {
    $matrix = [
      '0.0.00',
      '0.0.0.256',
      ['passing an array'],
      '127.0.0.1',
      '::1/x',
    ];

    foreach ($matrix as $addr) {
      list($int, $err) = (new \CIDR\IPv6)->addrToInt($addr);
      $this->assertEquals(true, $err, $addr);
    }
  }

  function testToIntReturnsCorrectIntegers() {
    $matrix = [
      ['::',                                      bchexdec('00000000000000000000000000000000')],
      ['::ffff',                                  bchexdec('0000000000000000000000000000ffff')],
      ['0000:0000:0000:0000:0000:0000:0000:0000', bchexdec('00000000000000000000000000000000')],
      ['ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', bchexdec('ffffffffffffffffffffffffffffffff')],
      ['fe80::42:acff:fe11:70',                   bchexdec('fe800000000000000042acfffe110070')],
    ];

    foreach ($matrix as $row) {
      list($int, $err) = (new \CIDR\IPv6)->addrToInt($row[0]);
      $this->assertEquals(null, $err, json_encode($row));
      $this->assertEquals($row[1], $int, json_encode($row));
    }
  }

  function testNetmaskGeneratesErrors() {
    $matrix = [
      229,
      '123',
      'abc',
    ];

    foreach ($matrix as $netmask) {
      list($netmask, $err) = (new \CIDR\IPv6)->netmask($netmask);
      $this->assertEquals(true, $err, $netmask);
    }
  }

  function testNetmask() {
    $matrix = [
      [64, bchexdec('ffffffffffffffff0000000000000000')],
      [0,  bchexdec('00000000000000000000000000000000')],
      [16, bchexdec('ffff0000000000000000000000000000')],
    ];

    foreach ($matrix as $row) {
      list($netmask, $err) = (new \CIDR\IPv6)->netmask($row[0]);
      $this->assertEquals(null, $err, json_encode($row));
      $this->assertEquals($row[1], $netmask, json_encode($row));
    }
  }

  function testMatchGeneratesErrors() {
    $matrix = [
      ['::1/224',  '::1'],
      ['::g/24', '::1'],
      ['::1/24',   '::fffff'],
      ['::1/abc',  '::1'],
    ];

    foreach ($matrix as $row) {
      list($match, $err) = \CIDR\IPv6::match($row[0], $row[1]);
      $this->assertEquals(true, $err, json_encode($row));
    }
  }

  function testMatch() {
    $matrix = [
      ['fe80::42:acff:fe11:70/64',   'fe80::42:acff:fe11:70',   true],
      ['fe80::42:acff:fe11:70/64',   'fe80::43:acff:fe11:70',   true],
      ['::1/0',                      'fe80::1',                 true],
      ['fe80::1:42:acff:fe11:70/64', 'fe80::2:42:acff:fe11:70', false],
      ['fe80::42:acff:fe11:70',      'fe80::42:acff:fe11:71',   false],
      ['fe80::42:acff:fe11:70',      'fe80::42:acff:fe11:70',   true],
    ];

    foreach ($matrix as $row) {
      list($match, $err) = \CIDR\IPv6::match($row[0], $row[1]);
      $this->assertEquals(null, $err, json_encode($row));
      $this->assertEquals($row[2], $match, json_encode($row));
    }
  }
}
