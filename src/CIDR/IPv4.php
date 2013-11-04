<?php

namespace CIDR;

class IPv4 {
  static function addr_to_int($addr) {
    if (!is_string($addr)) {
      return [0, true];
    }

    $numbers = explode('.', $addr);
    if (count($numbers) !== 4) {
      return [0, true];
    }

    $int = 0;
    $i = 1;

    foreach (array_reverse($numbers) as $num) {
      $num = (int)$num;

      if ($num > 255) {
        return [0, true];
      }

      $int += $num * $i;

      $i *= 256;
    }

    return $int;
  }
}
