class sententiaregum::frontend::npm($packages = []) {
  validate_array($packages)

  package { $packages:
    provider => npm,
    require  => Class['::sententiaregum::frontend::node', '::nodejs'],
    ensure   => present,
  }
}
