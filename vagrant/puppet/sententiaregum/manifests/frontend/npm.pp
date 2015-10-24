class sententiaregum::frontend::npm($packages = []) {
  validate_array($packages)

  class { '::nodejs': }

  package { $packages:
    provider => npm,
    require  => Class['::nodejs'],
    ensure   => present,
  }
}
