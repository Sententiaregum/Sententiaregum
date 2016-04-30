Exec { path => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin' }
Class['::apt::update'] -> Package <| |>

hiera_include('classes')
