class sententiaregum::backend::php(
  $version    = '5.6',
  $extensions = {},
  $timezone   = 'UTC'
) {
  validate_string($version)
  validate_hash($extensions)
  validate_string($timezone)

  case $version {
    '5.6':   { $ppa = 'php5-5.6' }
    '7.0':   { $ppa = 'php-7.0' }
    default: {
      fail('PHP versions 5.6 and 7.0 are allowed only!')
    }
  }

  ::apt::key { '4F4EA0AAE5267A6C': }
  ::apt::ppa { "ppa:ondrej/${ppa}":
    require => Apt::Key['4F4EA0AAE5267A6C']
  }

  class { '::php':
    service => 'apache',
  }

  create_resources('::php::module', $extensions)
}
