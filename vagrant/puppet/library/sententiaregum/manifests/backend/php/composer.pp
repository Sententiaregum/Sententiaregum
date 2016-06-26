class sententiaregum::backend::php::composer($options = undef, $timeout = 1000) {
  validate_string($options)
  validate_integer($timeout)

  $environment = get_composer_environment($::sententiaregum::ssh::env)

  exec { 'composer install':
    command     => "composer install ${options}",
    cwd         => '/var/www/sententiaregum',
    user        => 'vagrant',
    timeout     => $timeout,
    environment => $environment,
    require     => [
      Class['::sententiaregum::frontend::npm'],
      Class['::sententiaregum::infrastructure::mysql'],
      Class['::php'],
      Class['::sententiaregum::ssh'],
      Class['::timezone'],
      Package['webpack'],
    ],
  }
}
