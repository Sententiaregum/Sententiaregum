class sententiaregum::frontend::node($version = 'stable', $make_install = false) {
  validate_bool($make_install)
  validate_string($version)

  class { '::nodejs':
    version      => $version,
    make_install => $make_install,
  }
}
