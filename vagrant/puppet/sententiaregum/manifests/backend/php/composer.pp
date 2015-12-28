class sententiaregum::backend::php::composer {
  class { '::composer':
    command_name => 'composer',
    auto_update  => true,
    require      => Package['php5'],
    target_dir   => '/usr/local/bin',
  }

  exec { 'composer install':
    command     => 'composer install',
    cwd         => '/var/www/sententiaregum',
    user        => 'vagrant',
    environment => ['HOME=/home/vagrant'],
    require     => [
      Class['::composer'],
      Class['::sententiaregum::frontend::node'],
      Class['::sententiaregum::frontend::npm'],
      Class['::sententiaregum::infrastructure::mysql'],
      Class['::sententiaregum::backend::php'],
      Package['webpack'],
    ],
  }
}
