class sententiaregum::backend::ruby($version = '2.0') {
  validate_string($version)
  enforce_major_release(2, $version)

  $package       = "ruby${version}"
  $devel_package = "${package}-dev"

  ::apt::ppa { 'ppa:brightbox/ruby-ng': }
  ensure_packages([$package, $devel_package], {
    require => ::Apt::Ppa['ppa:brightbox/ruby-ng']
  })
}
