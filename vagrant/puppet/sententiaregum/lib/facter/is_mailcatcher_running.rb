Facter.add(:is_mailcatcher_running) do
  output = Facter::Util::Resolution.exec('netstat -ntpl | grep 1025')

  setcode do
    output =~ /1025/
  end
end
