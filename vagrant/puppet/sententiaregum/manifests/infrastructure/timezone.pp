class sententiaregum::infrastructure::timezone($zone = hiera('timezone')) {
  validate_string($zone)

  class { '::timezone':
    timezone => $zone,
  }
}
