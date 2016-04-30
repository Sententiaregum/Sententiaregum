class sententiaregum::infrastructure::mysql($databases = {}) {
  validate_hash($databases)

  include ::mysql::server
  create_resources('::mysql::db', $databases)
}
