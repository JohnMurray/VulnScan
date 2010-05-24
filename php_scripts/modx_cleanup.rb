#!/usr/bin/ruby

require 'rubygems'
require 'fileutils'


#remove duplicate entries (can't figure out how they got in there, but they both point to same file)
begin
    f = File.new( 'pluginStats.csv', 'r' )
    fw = File.new( 'pluginStats.csv.copy', 'w')
    newFileContents = Array.new
    while line = f.gets
        if newFileContents.length > 2
           if line != newFileContents.last
                newFileContents.push( line )
            end
        else
            newFileContents.push( line )
        end
    end
    newFileContents.each { |line| fw.write(line) }

    f.close
    fw.close
    File.delete( 'pluginStats.csv' )
    File.rename( 'pluginStats.csv.copy', 'pluginStats.csv' )
    
rescue => err
    puts "Exception: #{err}"
end

#change all .txt files to be .php files
begin
    fw = File.new( 'pluginStats.csv.copy', 'w' )
    f = File.new( 'pluginStats.csv', 'r' )
    while line = f.gets
        plugin_stats = line.split(',')
        puts line
        if plugin_stats[3] =~ /\.txt$/
            old_filename = plugin_stats[3]
            plugin_stats[3] = plugin_stats[3].gsub(/\.txt$/, '.php')
            File.rename( old_filename, plugin_stats[3] )
        end
        fw.write(plugin_stats[0] + ',' + plugin_stats[1] + ',' + plugin_stats[2] + ',' + plugin_stats[3] + ',' + "\n")
    end
    fw.close
    f.close
    File.delete( 'pluginStats.csv' )
    File.rename( 'pluginStats.csv.copy', 'pluginStats.csv' )
rescue => err
    puts "Exception: #{err}"
    err
end
