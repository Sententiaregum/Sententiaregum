class sententiaregum::backend::ruby($version) {
  validate_string($version)
  enforce_major_release(2, $version)

  $package       = "ruby${version}"
  $devel_package = "ruby${version}-dev"

  ::apt::ppa { 'ppa:brightbox/ruby-ng': } ->
  package { [$package, $devel_package]:
    ensure => installed,
  }
}
