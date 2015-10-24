class sententiaregum::frontend::ruby($packages = []) {
  validate_array($packages)

  package { $packages:
    provider => gem,
    ensure   => present,
    require  => [
      Package['ruby-dev'],
      Package['build-essential']
    ],
  }
}
