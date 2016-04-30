Facter.add(:is_mailcatcher_running) do
  setcode do
    Facter::Util::Resolution.exec('netstat -ntpl | grep 1025') =~ /1025/
  end
end
