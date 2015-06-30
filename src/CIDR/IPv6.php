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

    $addr = strtolower($addr);

    // Parse the string

    $ast = [];
    $pos = 0;

    for ($i = 0; $i < strlen($addr); $i++) {
      $c = substr($addr, $i, 1);

      $prev = $pos < 1 ? 0 : $pos - 1;

      if ($c === ':') {
        if (isset($ast[$prev]) && $ast[$prev] === ':') {
          $ast[$prev] = '::';
        } else {
          $ast[$pos] = ':';
          $pos++;
        }
      } elseif (in_array($c, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'])) {
        if (!isset($ast[$prev]) || $ast[$prev] === ':' || $ast[$prev] === '::') {
          $ast[$pos] = $c;
          $pos++;
        } else {
          $ast[$prev] .= $c;
        }
      } else {
        return [0, true];
      }
    }

    // Strip the separators

    $ast = array_filter($ast, function ($a) { return $a !== ':'; });

    // Convert each block to an integer

    $blocks = [];
    $i = 0;

    foreach ($ast as $cur) {
      if ($cur === '::') {
        // fill in the blanks
        for ($j = 0; $j < 9 - count($ast); $j++) {
          $blocks[] = 0;
        }
      } else {
        $val = hexdec($cur);
        if ($val > 0xffff) {
          return [0, true];
        }
        $blocks[] = $val;
      }

      $i++;
    }

    // Add together

    $int = 0;

    for ($i = 0; $i < count($blocks); $i++) {
      $int = bcadd($int, bcmul($blocks[$i], bcpow(2, (7-$i) * 16)));
    }

    return [$int, null];
  }
}
