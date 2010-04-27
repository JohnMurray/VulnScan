#!/usr/bin/ruby
require 'fileutils'
require 'UnArchive.rb'
require 'timeout'


#----------------------------------------------------------------
# Define all Constants for Program
#----------------------------------------------------------------
INPUT_FILE = "pluginStats.csv"
OUTPUT_FILE = "PluginVulnStats.csv"
HUMAN_FILE = "PluginVulnStats.list"
ERROR_FILE = "PluginVulnErrors.list"
TIMEOUT_DURATION = 20 * 60 #timeout for SCA scan in seconds

#----------------------------------------------------------------
# Create an array, iterate through csv file and put into array
#----------------------------------------------------------------

plugins = Array.new

begin 
    if File.exist?(INPUT_FILE)
        f = File.new(INPUT_FILE, "r")
        i = 0
        while (line = f.gets)
            plugins[i] = line.split(',')
            i += 1
        end
        plugins.delete_at(0)
    else
        #exit the code, the file does not exist
        puts "The file" + INPUT_FILE + "does not exist!"
        Process.exit!
    end
    
rescue
    puts "There was an error reading from the file: " + INPUT_FILE
    Process.exit!
end


#---------------------------------------------------------------
# create csv file for output
#---------------------------------------------------------------

f = File.new(OUTPUT_FILE, 'w')
f.write("Plugin Name,Version,URL,SLOC,Total Vulnerabilities,Vulnerability Density,\n")
f.close

#---------------------------------------------------------------
# Loop through array, scan all files and create stats file
#---------------------------------------------------------------

# plugins array layout
#   0 => Name
#   1 => Version
#   2 => URL
#   3 => File Name

plugins.each do |plugin|

    #if there is no file, report an error and continue with the next plugin
    if !File.exist?(plugin[3])
        ef = File.new(ERROR_FILE, 'a')
        ef.write(plugin[0] + '\n' + '   => No file exists\n\n')
        ef.close
        next
    end
    
    #define the name of the folder to move the plugin into
    dir = plugin[0] + '_' + plugin[1]

    #check if the file is already a directory (if so, skipping some steps)
    if File.directory?(plugin[3])
        File.rename( plugin[3], dir )
        Dir.chdir(dir)
    else
        #create directory for each plugin and move into that directory
        Dir.mkdir(dir)
        FileUtils.mv(plugin[3], dir)
        Dir.chdir(dir)        
    end

    begin
        #unpack
        UnArchive.unpack(plugin[3]) 
        
        #create fpr file with Fortify SCA
        Timeout::timeout(TIMEOUT_DURATION) {
            `sourceanalyzer -b #{dir} "./**/*.php"`
            `sourceanalyzer -b #{dir} -scan -f #{dir}.fpr`
        }
    rescue Timeout::Error
        ef = File.new(ERROR_FILE, 'a')
        ef.write(plugin[0] + "\n  => Scan timeout\n\n")
        ef.close
        next
    rescue
        ef = File.new(ERROR_FILE, 'a')
        ef.write(plugin[0] + "\n  => UnArchive error\n\n")
        ef.close
        next
    end

    #get type and sloccount
    Dir.mkdir("temp")
    File.copy(/*.fpr/, "temp")
    Dir.chdir("temp")
    %x[unzip *.fpr] #NOT SURE HOW TO IN RUBY NATIVELY FROM CORE
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
    f = File.open(HUMAN_FILE, 'a')
    f.write(dir + "\n")
    f.write("Total Vulnerabilities : " + vuln + "\n")
    f.write("SLOC Count : " + phpSloc + "\n")
    f.write("Vulnerability Density : " + vulnDensity + "\n\n\n")
    f.close

    #create a csv file
    f = File.open(OUTPUT_FILE, 'a')
    f.write(plugin[0] + ',')
    f.write(plugin[1] + ',')
    f.write(plugin[2] + ',')
    f.write(phpSloc + ',')
    f.write(vuln + ',')
    f.write(vulnDensity + ',' + "\n")
    f.close


    Dir.chdir("../")

end
