#!/bin/bash

require 'rubygems'
require 'fileutils'


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
