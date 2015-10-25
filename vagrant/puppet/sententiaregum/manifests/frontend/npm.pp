class sententiaregum::frontend::npm($packages = []) {
  validate_array($packages)

  class { '::nodejs':
    version      => 'stable',
    make_install => false
  }

  package { $packages:
    provider => npm,
    require  => Class['::nodejs'],
    ensure   => present,
  }
}
