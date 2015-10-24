class sententiaregum::infrastructure::redis($instances = {}) {
  validate_hash($instances)

  class { '::redis': }
  create_resources('::redis::instance', $instances)
}
