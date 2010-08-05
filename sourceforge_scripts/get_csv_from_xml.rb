require 'rubygems'
require 'hpricot'
require 'progressbar'

XML_FILE = 'sourceforge_data.xml'
CSV_FILE = 'sourceforge_data.csv'

#load in the file to parse into an Hpricot Element
begin
    xml = Hpricot( File.open(XML_FILE) )
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
pbar = ProgressBar.new("xml2csv", (xml/:project).each.count)
(xml/:project).each do |p|
    name = p.attributes['name']
    url = p.attributes['url']
    total = 0
    
    #iterate through items and get total download count
    (p/:item).each do |it|
        total += (it/:downloads)[0].inner_html.to_s.gsub(/[ ,]+/, '').to_i
    end

    #write to csv file
    File.open(CSV_FILE, 'a') {|f|
        f.write(name + ',' + url + ',' + total.to_s + ',');
        f.write("\n");
    }
    pbar.inc
end
pbar.finish
