module Puppet::Parser::Functions
  newfunction(:enforce_major_release) do |args|
    major_release_number = args[0].to_s
    actual_release       = args[1]

    unless actual_release =~ /^#{major_release_number}\.*/
      raise Puppet::ParseError, ("enforce_major_release(): Given release #{actual_release} does not match major release #{major_release_number}")
    end
  end
end
