#!/usr/bin/ruby

require 'rubygems'
require 'fileutils'


#remove duplicate entries (can't figure out how they got in there, but they both point to same file)
begin
    f = File.new( 'pluginStats.csv', 'r' )
    fw = Filew.new( 'plginStats.csv.copy', 'w')
    newFileContents = Array.new
    while line = f.gets
        newFileContents.push( line )
        if newFileContents.length > 2
            
        end
    end
rescue => err
    puts "Exception: #{err}"
end


begin
    fw = File.new( 'pluginStats.csv.copy', 'w' )
    f = File.new( 'pluginStats.csv', 'r' )
    while line = f.gets
        plugin_stats = line.split(',')
        if plugin_stats[3] =~ /\.txt$/
            old_filename = plugin_stats[3]
            plugin_stats[3] = plugin_stats[3].gsub(/\.txt$/, '.php')
            File.rename( old_filename, plugin_stats[3] )
        end
        fw.write(plugin_stats[0] + ',' + plugin_stats[1] + ',' + plugin_stats[2] + ',' + plugin_stats[3] + ',' + "\n")
    end
    fw.close
    f.close
    File.delete( 'plugin_stats.csv' )
    File.rename( 'plugin_stats.csv.copy', 'plugin_stats.csv' )
rescue => err
    puts "Exception: #{err}"
    err
end
