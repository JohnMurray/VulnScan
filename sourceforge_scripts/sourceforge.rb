#!/usr/bin/ruby

require 'rubygems'
require 'mechanize'
require 'hpricot'
require 'progressbar'


BASE_URL = "http://www.sourceforge.net"
ENTRY_PAGE = "http://www.sourceforge.net/softwaremap/?type_of_search=soft&sort=latest_file_date&sortdir=desc&limit=100&offset="
OUTPUT_FILE = "sourceforge_data.xml"
ERROR_FILE = "sourceforge_errors.txt"


##--------------------------------------------------------
## Function: generate_download_data
## Purpose: gen. download data given a project home-page
##          on sourceforge
##--------------------------------------------------------
def generate_download_data(project_url, project_name)
    
    begin
        h = Hpricot( Mechanize.new.get(project_url).body )
    rescue Exception => err
	File.open( ERROR_FILE, 'a') { |ef| ef.write("Error, unable to access page: #{project_url}\n\n")}
        puts "Error, unable to access page: #{project_url}"
        puts "Error message:\n\t#{err.message}"
	return
    end
    
    table = h.search("//table[@class='filesTable treeTable']")[0]
    
    #write the project header to the file
    File.open( OUTPUT_FILE, 'a' ) { |f| f.write( "<project name=\"#{project_name}\" url=\"#{project_url}\" >\n" ) }
    
    #set the depth and iterate through table of items (files/folders)
    depth = 0
    (table/:tr).each do |tr|
        if (tr/:td).size > 2
            name = ((tr/:td)[0]/:a)[0].inner_html
            size = (tr/:td)[2].inner_html
            date = (tr/:td)[3].inner_html
            downloads = (tr/:td)[4].inner_html.to_i

            #get depth
            if tr.attributes['class'] =~ /parent/i
                depth -= 1 if depth > 0
            elsif tr.attributes['class'] =~ /child-of-node/i
                depth += 1
            end

            #write to file
            File.open( OUTPUT_FILE, 'a' ) { |f| 
                f.write( "\t<item>\n" );
                f.write( "\t\t<name>" + name.to_s + "</name>\n" );
                f.write( "\t\t<size>" + size.to_s + "</size>\n" );
                f.write( "\t\t<date>" + date.to_s + "</date>\n" );
                f.write( "\t\t<downloads>" + downloads.to_s + "</downloads>\n" );
                f.write( "\t\t<depth>" + depth.to_s + "</depth>\n" );
                f.write( "\t</item>\n" );
            }
        end
    end
    File.open( OUTPUT_FILE, 'a' ) { |f| f.write( "</project>\n" ) }

end


##--------------------------------------------------------
## Start execution of the script here
##--------------------------------------------------------


#define agent for the entry page
agent = Mechanize.new

#determine the number of pages
page = agent.get( ENTRY_PAGE + "0" )
num_pages = 0
page.links.each do |link|
    if link.href =~ /\/softwaremap\/\?/
        if link.text.to_i > 10
            num_pages = link.text.to_i
        end
    end
end

fail "Could not retrieve number of pages." if num_pages == 0

#iterate for each listing page (with progressbar)
pbar = ProgressBar.new("Sourceforge Data", num_pages)
0.upto( num_pages - 1 ) do |page_num|
    agent = Mechanize.new
    listing_page = agent.get( ENTRY_PAGE + (page_num * 100).to_s )
    #iterate through each project listing on the listing page
    listing_page.links.each do |project_home_link|
        if project_home_link.href =~ /^\/projects\/[^\/]+\/$/
           
           #generate a link to the projects files page
           project_home_link_full = BASE_URL + project_home_link.href + "files/"

           #collect data on project
           generate_download_data( project_home_link_full, project_home_link.text )
        end
    end
    pbar.inc
end
pbar.finish
