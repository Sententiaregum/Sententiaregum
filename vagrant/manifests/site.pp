class application {
  contain sententiaregum::installs
  contain sententiaregum::backend::php
  contain sententiaregum::backend::server
  contain sententiaregum::infrastructure::mailcatcher
  contain sententiaregum::infrastructure::mysql
  contain sententiaregum::infrastructure::redis
  contain sententiaregum::infrastructure::jobs
  contain sententiaregum::frontend::npm
}

class { 'application': }
