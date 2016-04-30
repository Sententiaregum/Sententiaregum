class sententiaregum::infrastructure::jobs($schedules = {}) {
  validate_hash($schedules)
  create_resources('cron', $schedules)
}
