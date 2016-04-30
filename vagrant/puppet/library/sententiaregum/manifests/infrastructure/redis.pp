class sententiaregum::infrastructure::redis($instances = {}) {
  validate_hash($instances)

  include ::redis
  create_resources('::redis::instance', $instances)
}
