class sententiaregum::frontend($npm_packages, $ruby_packages) {
  validate_array($npm_packages)
  validate_array($ruby_packages)

  class { '::nodejs':
    version      => 'stable',
    make_install => false,
  } ->
  package { $npm_packages:
    provider => npm,
    require  => Class['::nodejs'],
    ensure   => present,
  }

  if !empty($ruby_packages) {
    package { $ruby_packages:
      provider => gem,
      require  => [
        Package['ruby-dev'],
        Package['build-essential'],
      ],
    }
  }
}
