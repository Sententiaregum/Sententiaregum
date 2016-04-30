class sententiaregum::infrastructure::mailcatcher($dest_ip) {
  validate_string($dest_ip)
  validate_ip_address($dest_ip)

  ensure_packages(['mailcatcher'], {
    provider => gem,
    require  => [
      Class['::sententiaregum::backend::ruby'],
      Package['build-essential'],
      Package['libsqlite3-dev'],
    ]
  })

  if !$::is_mailcatcher_running {
    exec { 'start mailcatcher':
      command => "mailcatcher --http-ip ${dest_ip}",
      require => [
        Package['mailcatcher'],
      ],
    }
  }
}
