Facter.add(:is_mailcatcher_running) do
  is_1025_in_use = Facter::Util::Resolution.exec('netstat -ntpl | grep 1025') =~ /1025/

  setcode do
    is_1025_in_use ? 1 : 0
  end
end
