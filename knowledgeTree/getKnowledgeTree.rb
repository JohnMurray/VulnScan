#!/usr/bin/ruby
#

f = File.new("KnowledgeTreeFiles.txt", 'r')

f.each {|line|
    `wget #{line}`
}

