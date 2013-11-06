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

    return [$int, null];
  }

  static function block_to_ip_and_netmask($block) {
    $split = explode('/', $block);

    if (count($split) !== 2) {
      return ['', 0, true];
    }

    $ip = $split[0];
    $netmask = (int)$split[1];

    if ($netmask < 0 || $netmask > 32) {
      return ['', 0, true];
    }

    // 0 => 00000000000000000000000000000000
    // 1 => 10000000000000000000000000000000
    // 2 => 11000000000000000000000000000000
    // 3 => 11100000000000000000000000000000
    // etc
    $_netmask = pow(2, $netmask)-1 << (32-$netmask);

    return [$ip, $_netmask, null];
  }
}