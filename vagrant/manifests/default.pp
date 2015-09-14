# installing required packages
class { 'apt': }

$requiredPackages = [
  'vim',
  'git',
  'wget',
  'curl',
  'bash-completion',
  'ruby-dev',
  'software-properties-common',
  'python-software-properties'
]

package { $requiredPackages:
  ensure => installed
}

$ppaKey = '4F4EA0AAE5267A6C'
apt::key { $ppaKey: }
apt::ppa { 'ppa:ondrej/php5-5.6':
  require => Apt::Key[$ppaKey]
}

exec { 'apt-get upgrade':
  command => '/usr/bin/apt-get -y upgrade'
}

# vhost
class { 'apache': }
apache::module { 'rewrite': }

$host_name = 'sententiaregum.dev'
apache::vhost { $host_name:
  server_name => $host_name,
  port => 80,
  docroot => '/var/www/sententiaregum/web'
}

# frontend
class { 'nodejs':
  version => 'stable',
  make_install => false
}

package { 'grunt-cli':
  provider => npm,
  require => Class['nodejs']
}

$rubyRequirements = [
  Package['ruby-dev'],
  Package['build-essential']
]

package { 'compass':
  provider => gem,
  require => $rubyRequirements
}

package { 'sass':
  provider => gem,
  require => $rubyRequirements
}

# backend
# clear module prefix in order to prevent conflicts with the php-apc package
class { 'php':
  service => 'apache',
  module_prefix => ''
}

class { 'php::devel':
  require => Class['php'],
}

php::ini { 'php.ini customizations':
  value => [
    'date.timezone = "UTC"',
    'display_errors = On',
    'error_reporting = -1',
  ],
  notify => Service['apache'],
  require => Class['php']
}

php::module { 'php5-gd': }
php::module { 'php5-xdebug': }
php::module { 'php5-cli': }
php::module { 'php5-mysql': }
php::module { 'php5-curl': }
php::module { 'php5-intl': }
php::module { 'php5-mcrypt': }
php::module { 'php-apc': }

class { 'composer':
  command_name => 'composer',
  auto_update => true,
  require => Package['php5', 'curl'],
  target_dir => '/usr/local/bin'
}

# infrastructure/database
class { 'redis': }
redis::instance { 'redis-doctrine-cache':
  redis_port => 6900,
}

redis::instance { 'redis-queue-mechanism':
  redis_port => 6901
}

# use an empty root password, since travis does that, too (we don't have password conflicts in functional tests)
class { 'mysql::server':
  override_options => { 'root_password' => '', },
}

$dbname = 'sententiaregum'
$dbname_test = 'sententiaregum_test'
mysql::db { $dbname:
  user => 'dev',
  password => 'dev',
  host => 'localhost',
  grant => ['ALL']
}

mysql::db { $dbname_test:
  user => 'test',
  password => 'test',
  host => 'localhost',
  grant => ['ALL']
}
