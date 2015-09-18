class sententiaregum::infrastructure($database_name, $use_mail_catcher, $build_redis) {
  validate_string($database_name)
  validate_bool($use_mail_catcher)
  validate_bool($build_redis)

  if $database_name != undef {
    class { '::mysql::server':
      override_options => { 'root_password' => '', },
    }

    $testDatabaseName = "${database_name}_test"

    mysql::db { $database_name:
      user     => 'dev',
      password => 'dev',
      host     => 'localhost',
      grant    => ['ALL'],
    }

    mysql::db { $testDatabaseName:
      user     => 'test',
      password => 'test',
      host     => 'localhost',
      grant    => ['ALL'],
    }
  }

  if $use_mail_catcher {
    package { 'mailcatcher':
      provider => gem,
      require  => [
        Package['ruby-dev'],
        Package['build-essential'],
      ],
    }
  }

  if $build_redis {
    class { '::redis': }

    redis::instance { 'app-doctrine-cache':
      redis_port => 6900,
    }

    redis::instance { 'app-message-queue':
      redis_port => 6901,
    }
  }
}
