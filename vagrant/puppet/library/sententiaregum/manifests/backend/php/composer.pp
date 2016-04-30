class sententiaregum::backend::php::composer($options = undef) {
  validate_string($options)

  exec { 'composer install':
    command     => "composer install ${options}",
    cwd         => '/var/www/sententiaregum',
    user        => 'vagrant',
    environment => ['HOME=/home/vagrant'],
    timeout     => 500,
    require     => [
      Class['::composer'],
      Class['::sententiaregum::frontend::npm'],
      Class['::sententiaregum::infrastructure::mysql'],
      Class['::sententiaregum::backend::php'],
      Package['webpack'],
    ],
  }
}
