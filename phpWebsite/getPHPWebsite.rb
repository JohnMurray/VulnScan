#!/usr/bin/ruby
#

f = File.new("phpFiles.txt", 'r')

f.each {|line|
    `wget #{line}`
}

