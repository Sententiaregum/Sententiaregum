define sententiaregum::deploy::capistrano::plugin {
  $gem_package_name = "capistrano-${name}"

  package { $gem_package_name:
    provider => gem,
    require  => [
      Class['::sententiaregum::backend::ruby'],
      Package['build-essential'],
    ],
  }
}
