#!/usr/bin/ruby

##-----------------------------------------------------------------------------
## INCLUDE ALL REQUIRED MODULES
##-----------------------------------------------------------------------------
require 'rubygems'
require 'mechanize'
require 'fileutils'
#require 'hpricot'



##-----------------------------------------------------------------------------
## DEFINE ALL SCRIPT CONSTANTS
##-----------------------------------------------------------------------------
#define all fetch URLs
BASE_URL = Array.new()
BASE_URL[0] = 'http://squirrelmail.org/plugins_category.php?category_id=all'
BASE_URL[1] = 'http://wordpress.org/extend/plugins/browse/new/page/'
BASE_URL[2] = 'http://www.mediawiki.org'
FILENAME = 'pluginStats.csv'
ERROR_FILE = 'fetch_errors.txt'
CHROME_USER_AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5'



##-----------------------------------------------------------------------------
## FUNCTION: main
## PURPOSE: execute fetch script
##-----------------------------------------------------------------------------
def main()

	#check flags from command line (currently statically implemented)
	page_to_crawl = BASE_URL[2]

	#create file for reporting to
	write_headers_to_file()

	#determine function to call for page crawling
	case page_to_crawl
		when BASE_URL[0]
			fetch_squirrelmail()
		when BASE_URL[1]
			fetch_wordpress(BASE_URL[1])
        when BASE_URL[2]
            fetch_media_wiki(BASE_URL[2])
	end
	
end



##-----------------------------------------------------------------------------
## FUNCTION: fetch_squirrelmail
## PURPOSE: fetch plugins from squirrelmail and write to file
##-----------------------------------------------------------------------------
def fetch_squirrelmail()
end



##-----------------------------------------------------------------------------
## FUNCTION: fetch_wordpress
## PURPOSE: fetch plugins from wordpress and write to file
##-----------------------------------------------------------------------------
def fetch_wordpress(base_url)

	#Fetch the total number of plugin pages from wordpress' website
	num_of_plugin_pages = 0
	agent = Mechanize.new
	page = agent.get(base_url + '1/')
	page.links.each do |link|
		if link.href =~ /extend\/plugins\/browse\/new\/page\//
			if link.text.to_i > 500
				num_of_plugin_pages = link.text.to_i
			end
		end
	end

	#loop through all of the pages and get all of the needed data
	(1..num_of_plugin_pages).each do |i|
		#define a browser agent for searching web pages
		agent = :Mechanize.new
		page0 = agent.get(base_url + i.to_s + "/")
		page0.links.each do |link0|

			next if link0.href =~ /\/extend\/plugins\/tags\// ||
				link0.href =~ /\/extend\/plugins\/...about\// ||
				link0.href =~ /\/extend\/plugins\/browse\/.+\//

			#verify that we are following the right links
			if link0.href =~ /\/extend\/plugins\/[a-z0-9_\-]{1,}/

				#get and verify the link
				begin
					page1 = agent.get(link0.href)
				rescue Exception => e
					puts "\t\tcould not get file"
					puts "\t\t" + e.message
					next
				end

				#collect individual plugin data
				page1.links.each do |link1|
					if link1.href =~ /\.zip/
						#try to get the file and collect data
						begin
							#download file
							pluginPage = agent.get(link1.href)
							wgetFile = File.new(File.basename(link1.href), 'w')
							wgetFile.write(pluginPage.body)
							wgetFile.close

							#write information to the results array
							results= Array.new(4)
							temp_page_title = page1.title[12, page1.title.length - 1]
							results[0] = temp_page_title.split("\u00AB")[0] #plugin name
							results[2] = link0.href #url
							pluginSplit = link1.href.split('/')
							results[3] = pluginSplit[pluginSplit.length - 1] #file name

							#get version
							hp = Hpricot(page1.body).search("li")
							hp.each do |item|
								if item.innerText =~ /Version:/
									results[1] = item.innerText[9, item.innerText.length - 1] #version
									break
								end
							end

							#write out results to file
							write_to_file(results)

						rescue Exception => e
							errorFile = File.new("fetch_errors.txt", "a")
							errorFile.write( "Plugin Name: " + page1.title + "\n" )
							errorFile.write( "\t=> " + link1.href + "\n" )
							errorFile.write( "\t=> " + e.message + "\n\n")
							errorFile.close
						end
					end
				end
			end
		end
	end
end





def fetch_media_wiki(base_url)

    #starting with the first page, loop through all plugin pages
    agent = Mechanize.new
    agent.user_agent = CHROME_USER_AGENT
    outer_page = agent.get(base_url + '/wiki/Special:AllPages/Extension:')

    next_page = ''

    begin
    
        current_page = next_page
        
        if(current_page != '')
            agent = Mechanize.new
            agent.user_agent = CHROME_USER_AGENT
            outer_page = agent.get(base_url + current_page)
        end

        outer_page.links.each do |link|
            #see if we are on the last page
            if link.text =~ /^Next page \(/
                next_page = link.href

            #check if we are on a extension
            elsif link.href =~ /\/wiki\/Extension:/
                inner_page = agent.get(base_url + link.href)

                #start collecting stats on plugins
                plugin_items = Array.new(4)

                plugin[0] = link.text #name
                plugin[2] = base_url + link.href #URL

                #loop through all links in inner page to find download
                

            end
        end

    end while next_page != current_page

end




def write_to_file(row)

	file = File.new(FILENAME, "a")
	file.write(row[0] + ',' + row[1] + ',' + row[2] + ',' + row[3] + ',' + "\n")
	file.close

end



def write_headers_to_file()

	file = File.new(FILENAME, "w")
	file.write('Plugin Name, Version, Url, File Name,,' + "\n")
	file.close

end



#does nothing for now
def get_command_line_arguments()

	args = Array.new
	ARGV.each do |a|
		file.write()
	end

end




#execute the script
main()
