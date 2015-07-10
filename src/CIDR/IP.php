<?php

namespace CIDR;

class IP {
  static function match($haystack, $needle) {
    $a = explode('/', $haystack);

    $haystack_addr = $a[0];

    if (\CIDR\IPv6::valid($haystack_addr) && \CIDR\IPv6::valid($needle)) {
      return \CIDR\IPv6::match($haystack, $needle);
    }

    if (\CIDR\IPv4::valid($haystack_addr) && \CIDR\IPv4::valid($needle)) {
      return \CIDR\IPv4::match($haystack, $needle);
    }

    if (
      (\CIDR\IPv6::valid($haystack_addr) || \CIDR\IPv4::valid($haystack_addr))
      &&
      (\CIDR\IPv4::valid($haystack_addr) || \CIDR\IPv6::valid($haystack_addr))
    ) {
      return [false, null];
    }

    return [null, 'one or more addresses provided were invalid'];
  }
}
