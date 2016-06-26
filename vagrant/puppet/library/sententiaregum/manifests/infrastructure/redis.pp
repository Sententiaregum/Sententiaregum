class sententiaregum::infrastructure::redis {
  ensure_packages(['redis-server'])
  ensure_resource('service', 'redis-server', {
    ensure  => running,
    require => Package['redis-server'],
  })
}
