<?php

namespace CIDR;

class IPv4 extends IPGeneric {
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
