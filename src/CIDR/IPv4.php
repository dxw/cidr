<?php

namespace CIDR;

class IPv4 {
  static function addrToInt($addr) {
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

      $i = $i << 8;
    }

    return [$int, null];
  }

  static function netmask($i) {
    if (!is_int($i)) {
      return [0, true];
    }

    if ($i < 0 || $i > 32) {
      return [0, true];
    }

    $netmask = pow(2, $i)-1 << (32-$i);

    return [$netmask, null];
  }

  static function match($haystack, $needle) {
    list($haystack_addr, $haystack_netmask) = explode('/', $haystack);

    list($_haystack_addr, $err) = self::addrToInt($haystack_addr);
    if ($err !== null) {
      return [null, $err];
    }

    list($_needle, $err) = self::addrToInt($needle);
    if ($err !== null) {
      return [null, $err];
    }

    if ($haystack_netmask === null) {
      return [$_haystack_addr === $_needle, null];
    } else {
      list($_haystack_netmask, $err) = self::netmask((int)$haystack_netmask);
      if ($err !== null) {
        return [null, $err];
      }

      $haystack_masked = $_haystack_addr & $_haystack_netmask;
      $needle_masked = $_needle & $_haystack_netmask;

      $match = $haystack_masked === $needle_masked;

      return [$match, null];
    }
  }
}
