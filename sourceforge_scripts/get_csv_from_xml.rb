require 'rubygems'
require 'hpricot'


XML_FILE = 'sourceforge_data.xml'
CSV_FILE = 'sourceforge_data.csv'

#load in the file to parse into an Hpricot Element
begin
    xml = Hpricot( File.open(XML_FIL) )
rescue
    Fail "Could not open data file for proccessing"
end


#prepare the csv file
begin
    File.open(CSV_FILE, 'a') { |f| f.write("name, url, downloads, \n"); }
rescue
    fail "Could not create csv data file for output"
end


#try to start iterating throught the file and getting all download counts
(h/:project).each do |p|
    name = p.attributes['name']
    url = p.attributes['url']
    total = 0
    
    #iterate through items and get total download count
    (p/:item).each do |it|
        total += (it/:download)[0].gsub(/[ ,]+/, '').to_i
    end

    #write to csv file
    File.open(CSV_FILE, 'a') {|f|
        f.write(name + ',' + url + ',' + total + ',');
    }
end
