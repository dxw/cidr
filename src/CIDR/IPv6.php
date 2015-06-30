<?php

namespace CIDR;

class IPv6 extends IPGeneric {
  function __construct() {
    $this->bits = 128;
  }

  function addrToInt($addr) {
    if (!is_string($addr)) {
      return [0, true];
    }

    $int = ip2long($addr);

    if ($int === false) {
      return [0, true];
    }

    return [$int, null];
  }
}
