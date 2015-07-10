<?php

class IPTest extends PHPUnit_Framework_TestCase {
  function testMatch() {
    $matrix = [
      ['0.0.00',         '::1',         true, null],
      ['0.0.0.256',      '::1',         true, null],
      ['::1/x',          '::1',         true, null],
      ['::1/1',          '::1',         false, true],
      ['::1',            '::1',         false, true],
      ['192.168.1.1/24', '192.168.1.2', false, true],
      ['192.168.1.1/24', '192.168.2.1', false, false],
      ['192.168.1.1/24', '::1',         false, false],
    ];

    foreach ($matrix as $row) {
      list($match, $err) = \CIDR\IP::match($row[0], $row[1]);
      $this->assertEquals($row[2], $err !== null, json_encode($row));
      $this->assertEquals($row[3], $match, json_encode($row));
    }
  }
}
