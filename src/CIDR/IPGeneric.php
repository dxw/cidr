<?php

namespace CIDR;

abstract class IPGeneric {
  abstract function addrToInt($addr);

  function netmask($i) {
    if (!is_int($i)) {
      return [0, true];
    }

    if ($i < 0 || $i > $this->bits) {
      return [0, true];
    }

    $netmask = pow(2, $i)-1 << ($this->bits-$i);

    return [$netmask, null];
  }

  static function match($haystack, $needle) {
    $a = explode('/', $haystack);

    $haystack_addr = $a[0];
    $haystack_netmask = count($a) > 1 ? $a[1] : null;

    list($_haystack_addr, $err) = (new static)->addrToInt($haystack_addr);
    if ($err !== null) {
      return [null, $err];
    }

    list($_needle, $err) = (new static)->addrToInt($needle);
    if ($err !== null) {
      return [null, $err];
    }

    if ($haystack_netmask === null) {

      return [$_haystack_addr === $_needle, null];

    } else {

      // Make sure string is valid int
      $haystack_netmask_i = (int)$haystack_netmask;
      if ($haystack_netmask !== (string)$haystack_netmask_i) {
        return [null, true];
      }

      list($_haystack_netmask, $err) = (new static)->netmask($haystack_netmask_i);
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
