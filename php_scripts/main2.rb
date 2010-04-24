#!/usr/bin/ruby
require 'fileutils'
require 'UnArchive.rb'

#----------------------------------------------------------------
# Create an array, iterate through csv file and put into array
#----------------------------------------------------------------

plugins = Array.new

begin
    
    if File.exist?("pluginStats.csv")
        f = File.new("pluginStats.csv", "r")
        i = 0
        while (line = f.gets)
            plugins[i] = line.split(',')
            i += 1
        end
        plugins.delete_at(0)
    else
        #exit the code, the file does not exist
        puts "The file pluginStats.csv does not exist"
        Process.exit!
    end
    
rescue
    puts "There was an error reading from the file pluginStats.csv"
    Process.exit!
end

#create csv file for output
f = File.new("PluginStats.csv", 'w')
f.write("Plugin Name,Version,URL,SLOC,Total Vulnerabilities,Vulnerability Density,\n") #%x[echo "Plugin Name,Version,URL,SLOC,Total Vulnerabilities,Vulnerability Density," > PluginStats.csv]
f.close

#---------------------------------------------------------------
# Loop through array, scan all files and create stats file
#---------------------------------------------------------------

plugins.each do |plugin|
    #create directory for each plugin and move into that directory
    dir = plugin[0] + "_" + plugin[1]
    Dir.mkdir(dir)
    FileUtils.mv(plugin[3], dir) #%x[mv #{plugin[3]} #{dir}]
    Dir.chdir(dir)

    #unpack file
    UnArchive.unpack(plugin[3])

    #create fpr
    %x[sourceanalyzer -b #{dir} "./**/*.php"]
    %x[sourceanalyzer -b #{dir} -scan -f #{dir}.fpr]

    #get type and sloccount
    Dir.mkdir("temp")
    File.copy(/*.fpr/, "temp")
    Dir.chdir("temp")
    %x[unzip *.fpr]                     #NOT SURE HOW TO (IF POSSIBLE) IN RUBY
    %x[fgrep '<Type>' audit.fvdl > ../VulnerabilityTypes.list;]
    Dir.chdir("../")
    FileUtils.rm_rf("temp")
    %x[sloccount * > SlocCount.list]

    #get vulnerability total
    vulnUnParsed =  File.new("VulnerabilityTypes.list").readlines.count  #%x[wc -l VulnerabilityTypes.list]
    vulnarray = vulnUnParsed.split()
    vuln = vulnarray[0].to_f

    #get SLOC count
    slocUnParsed = %x[grep 'php:' SlocCount.list]
    slocArray = slocUnParsed.split()
    phpSloc = slocArray[1].to_f

    #calculate vulnerability density
    vulnDensity = (vuln/phpSloc) * 1000

    #put all information to a file
    f = File.open("PluginStats.list", 'a')
    f.write(dir + "\n")         #%x[echo "#{dir}" >> ../PluginStats.list]
    f.write("Total Vulnerabilities : " + vuln + "\n")        #%x[echo "Total Vulnrabilities : #{vuln}" >> ../PluginStats.list]
    f.write("SLOC Count : " + phpSloc + "\n")                   #%x[echo "SLOC Count : #{phpSloc}" >> ../PluginStats.list]
    f.write("Vulnerability Density : " + vulnDensity + "\n\n\n")    #%x[echo "Vulnerability Density : #{vulnDensity}\n\n" >> ../PluginStats.list]
    f.close

    #create a csv file
    f = File.open("PluginStats.csv", 'a')
    f.write(plugin[0] + ',')  #%x[echo -n "#{plugin[0]}," >> ../PluginStats.csv]
    f.write(plugin[1] + ',')  #%x[echo -n "#{plugin[1]}," >> ../PluginStats.csv]
    f.write(plugin[2] + ',')  #%x[echo -n "#{plugin[2]}," >> ../PluginStats.csv]
    f.write(phpSloc + ',')  #%x[echo -n "#{phpSloc}," >> ../PluginStats.csv]
    f.write(vuln + ',')  #%x[echo -n "#{vuln}," >> ../PluginStats.csv]
    f.write(vulnDensity + ',')  #%x[echo "#{vulnDensity}," >> ../PluginStats.csv]
    f.close


    Dir.chdir("../")

end
