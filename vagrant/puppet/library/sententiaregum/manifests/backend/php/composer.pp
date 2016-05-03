class sententiaregum::backend::php::composer($options = undef, $timeout = 1000) {
  validate_string($options)
  validate_integer($timeout)

  $node_path = "NODE_PATH=/usr/local/node/node-default/lib/node_modules"
  if has_key($::sententiaregum::ssh::env, 'NODE_ENV') {
    $environment = ['HOME=/home/vagrant', "NODE_ENV=${::sententiaregum::ssh::env['NODE_ENV']}", $node_path]
  } else {
    $environment = ['HOME=/home/vagrant', $node_path]
  }

  exec { 'composer install':
    command     => "composer install ${options}",
    cwd         => '/var/www/sententiaregum',
    user        => 'vagrant',
    timeout     => $timeout,
    environment => $environment,
    require     => [
      Class['::composer'],
      Class['::sententiaregum::frontend::npm'],
      Class['::sententiaregum::infrastructure::mysql'],
      Class['::sententiaregum::backend::php'],
      Class['::sententiaregum::ssh'],
      Class['::timezone'],
      Package['webpack'],
    ],
  }
}
