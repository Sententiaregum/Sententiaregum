class sententiaregum::deploy::capistrano {
  package { 'capistrano':
    provider => gem,
    require  => [
      Class['::sententiaregum::backend::ruby'],
      Package['build-essential'],
    ],
  }
}
