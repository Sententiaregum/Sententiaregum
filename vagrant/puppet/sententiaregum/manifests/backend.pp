class sententiaregum::backend($use_composer, $php56, $ondrej_ppa_key) {
  validate_bool($use_composer)
  validate_bool($php56)
  validate_string($ondrej_ppa_key)

  if $php56 {
    if empty($ondrej_ppa_key) {
      fail('The ppa key for the ondrej-php5-5.6 archive must not be empty when using php 5.6')
    }

    apt::key { $ondrej_ppa_key: }
    apt::ppa { 'ppa:ondrej/php5-5.6':
      require => Apt::Key[$ondrej_ppa_key],
    }

    # apt-get upgrade is mandatory after adding the ppa
    exec { 'php::apt-get-upgrade':
      command => '/usr/bin/apt-get -y upgrade',
    }
  }
  else {
    if !empty($ondrej_ppa_key) {
      warn('The ppa key is not necessary when not using php 5.6')
    }
  }

  class { '::php':
    service => 'apache',
  }

  php::ini { 'php.ini customizations':
    value   => [
      'date.timezone = "UTC"',
      'display_errors = On',
      'error_reporting = -1',
    ],
    notify  => Service['apache'],
    require => Class['php'],
  }

  php::module { 'gd': }
  php::module { 'xdebug': }
  php::module { 'cli': }
  php::module { 'mysql': }
  php::module { 'curl': }
  php::module { 'intl': }
  php::module { 'mcrypt': }
  php::module { 'apc':
    module_prefix => 'php-',
  }

  if $use_composer {
    class { '::composer':
      command_name => 'composer',
      auto_update  => true,
      require      => Package['php5', 'curl'],
      target_dir   => '/usr/local/bin',
    }
  }
}
