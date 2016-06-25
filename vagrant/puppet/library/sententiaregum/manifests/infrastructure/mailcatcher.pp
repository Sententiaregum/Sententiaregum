class sententiaregum::infrastructure::mailcatcher($dest_ip) {
  validate_string($dest_ip)
  validate_ip_address($dest_ip)

  ensure_packages(['mailcatcher'], {
    provider => gem,
    require  => [
      Package['build-essential'],
      Package['libsqlite3-dev'],
    ]
  })

  if $facts['is_mailcatcher_running'] == 0 {
    exec { 'start mailcatcher':
      command => "mailcatcher --http-ip ${dest_ip}",
      require => [
        Package['mailcatcher'],
      ],
    }
  }
}
