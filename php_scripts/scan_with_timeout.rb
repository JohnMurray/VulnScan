#!/usr/bin/ruby

require 'timeout'
require 'fileutils'
require 'UnArchive.rb'

#csv = File.new("PluginStats.csv", 'w')
#csv.write("Plugin Name, Sloc, Total Vulnerabilities, Vulnerability density,\n")
#csv.close

duration = 60 * 20

Dir.glob("*.zip") {|file|
	dir = File.basename(file, ".zip")
	Dir.mkdir(dir)
	FileUtils.mv(file, dir)
	Dir.chdir(dir)
begin

	UnArchive.unpack(file)

	Timeout::timeout(duration){
		`sourceanalyzer -b #{dir} "./**/*.php"`
		`sourceanalyzer -b #{dir} -scan -f #{dir}.fpr`
	}

rescue Timeout::Error
	er = File.open("../TimeoutError.txt", 'a')
	er.write("#{dir} timed out\n")
	er.close
	Dir.chdir("../")
	next
end
	Dir.mkdir("tmp")
	fprName = dir + ".fpr"
	`cp #{fprName} tmp`
	#File.copy(fprName, "tmp")
	#File.copy(dir + ".fpr", "tmp")
	Dir.chdir("tmp")
	`unzip *.fpr`
	`fgrep '<Type>' audit.fvdl > ../VulnerabilityTypes.list;`
	Dir.chdir("../")
	FileUtils.rm_rf("tmp")
	`sloccount * > SlocCount.list`
	
	vulnUnParsed = File.new("VulnerabilityTypes.list").readlines.count
	#vulnArray = vulnUnParsed.split()
	#vuln = vulnArray[0].to_f
	vuln = vulnUnParsed.to_f

	slocUnParsed = %x[grep 'php:' SlocCount.list]
	slocArray = slocUnParsed.split()
	phpSloc = slocArray[1].to_f

	vulnDensity = (vuln/phpSloc) * 1000

	csv = File.open("../PluginStats.csv", 'a')
	csv.write(dir + ',')
	csv.write(phpSloc.to_s + ',')
	csv.write(vuln.to_s + ',')
	csv.write(vulnDensity.to_s + ',' + "\n") 
	csv.close
	Dir.chdir("../")
	#puts file
}

