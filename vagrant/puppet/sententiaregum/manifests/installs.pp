class sententiaregum::installs($packages) {
  validate_array($packages)

  class { '::apt': }

  if !empty($packages) {
    package { $packages:
      ensure => installed,
    }
  }
}
