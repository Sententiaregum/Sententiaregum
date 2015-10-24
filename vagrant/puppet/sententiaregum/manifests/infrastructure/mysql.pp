class sententiaregum::infrastructure::mysql($databases = {}) {
  validate_hash($databases)

  class { '::mysql::server': }

  create_resources('::mysql::db', $databases)
}
