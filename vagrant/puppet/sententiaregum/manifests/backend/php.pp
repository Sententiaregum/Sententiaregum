class sententiaregum::backend::php(
  $version    = '5.6',
  $extensions = {},
  $composer   = true,
  $timezone   = 'UTC'
) {
  validate_string($version)
  validate_hash($extensions)
  validate_bool($composer)
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

  exec { 'ppa::upgrade':
    command => '/usr/bin/apt-get -y upgrade',
  }

  class { '::php':
    service       => 'apache',
    module_prefix => ''
  }

  create_resources('::php::module', $extensions)

  class { '::composer':
    command_name => 'composer',
    auto_update  => true,
    require      => Package['php5'],
    target_dir   => '/usr/local/bin',
  }
}
