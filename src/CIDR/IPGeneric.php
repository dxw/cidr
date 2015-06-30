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

    // $netmask = pow(2, $i)-1 << ($this->bits-$i);
    $netmask = bcmul(bcsub(bcpow(2, $i), 1), bcpow(2, $this->bits-$i));

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

      return [bccomp($_haystack_addr, $_needle) === 0, null];

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

      $haystack_masked = static::bcAnd($_haystack_addr, $_haystack_netmask);
      $needle_masked = static::bcAnd($_needle, $_haystack_netmask);
      $match = bccomp($haystack_masked, $needle_masked) === 0;

      return [$match, null];
    }
  }

  // & that works with BC numbers
  static function bcAnd($a, $b) {
    // If a is longer than b, swap
    if (strlen($a) > strlen($b)) {
      $c = $b;
      $b = $a;
      $a = $c;
    }

    // Adjust a until it's as long as b
    while (strlen($b) > strlen($a)) {
      $a = '0' . $a;
    }

    // Convert to hex
    $_a = static::dec2hex($a);
    $_b = static::dec2hex($b);

    // & one block at a time
    $c = '';
    for ($i = 0; $i < strlen($_a); $i += 8) {
      $aBlock = substr($_a, $i, 8);
      $bBlock = substr($_b, $i, 8);

      $c .= hexdec($aBlock) & hexdec($bBlock);
    }

    // Convert to dec
    return static::hex2dec($c);
  }

  // http://php.net/manual/en/function.dechex.php#21086
  static function hex2dec($number)
  {
    $decvalues = array('0' => '0', '1' => '1', '2' => '2',
    '3' => '3', '4' => '4', '5' => '5',
    '6' => '6', '7' => '7', '8' => '8',
    '9' => '9', 'A' => '10', 'B' => '11',
    'C' => '12', 'D' => '13', 'E' => '14',
    'F' => '15');
    $decval = '0';
    $number = strrev($number);
    for($i = 0; $i < strlen($number); $i++)
    {
      $decval = bcadd(bcmul(bcpow('16',$i,0),$decvalues[$number{$i}]), $decval);
    }
    return $decval;
  }

  // http://php.net/manual/en/function.dechex.php#21086
  static function dec2hex($number) {
    $hexvalues = array('0','1','2','3','4','5','6','7',
    '8','9','A','B','C','D','E','F');
    $hexval = '';
    while($number != '0')
    {
      $hexval = $hexvalues[bcmod($number,'16')].$hexval;
      $number = bcdiv($number,'16',0);
    }
    return $hexval;
  }
}
