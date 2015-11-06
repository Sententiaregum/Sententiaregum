class sententiaregum::infrastructure::mailcatcher {
  package { 'mailcatcher':
    provider => gem,
    require  => [
      Package['ruby-dev'],
      Package['build-essential'],
      Package['libsqlite3-dev'],
    ],
  }
}
