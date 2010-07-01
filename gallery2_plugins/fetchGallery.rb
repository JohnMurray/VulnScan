#!/usr/bin/ruby

################################################
# Need to have "Gallery2_userContributions.txt" 
# file in same directory in order to download
# plugins that were randomly scattered
################################################


require 'rubygems'
require 'mechanize'
require 'hpricot'

BASE_URL = 'http://codex.gallery2.org/Gallery2:Download#Modules'

agent = WWW::Mechanize.new
page = agent.get(BASE_URL)
page.links.each do |link|
    if link.href =~ /\.gz/ && link.href =~ /module/
        puts link.href
        begin
            pluginPage = agent.get(link.href)
            wgetFile = File.new(File.basename(link.href)[10..-1], 'w')
            wgetFile.write(pluginPage.body)
            wgetFile.close
        rescue Exception => e
            errorFile = File.new("fetch_errors.txt", 'a')
            errorFile.write(File.basename(link.href) + "\n" + e.message + "\n\n")
        end

    end
end

puts "Finished getting Gallery 2 Plugins from Gallery Website"

f = File.new("Gallery2_userContributions.txt", 'r')
f.each {|line|
	`wget #{line}`
}
puts "Finished getting User Contributed Plug Ins"
