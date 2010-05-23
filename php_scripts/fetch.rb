#!/usr/bin/ruby

##-----------------------------------------------------------------------------
## INCLUDE ALL REQUIRED MODULES
##-----------------------------------------------------------------------------
require 'rubygems'
require 'mechanize'
require 'fileutils'
require 'hpricot'
require 'repository_module'



##-----------------------------------------------------------------------------
## DEFINE ALL SCRIPT CONSTANTS
##-----------------------------------------------------------------------------
#define all fetch URLs
BASE_URL = Array.new()
BASE_URL[0] = 'http://squirrelmail.org/plugins_category.php?category_id=all'
BASE_URL[1] = 'http://wordpress.org/extend/plugins/browse/new/page/'
BASE_URL[2] = 'http://www.mediawiki.org'
BASE_URL[3] = 'http://modxcms.com/extras/search.html?query=&start=290&limit=20'#'http://modxcms.com/extras/search.html?query=&limit=0'
FILENAME = 'pluginStats.csv'
ERROR_FILE = 'fetch_errors.txt'



##-----------------------------------------------------------------------------
## FUNCTION: main
## PURPOSE: execute fetch script
##-----------------------------------------------------------------------------
def main()

    #check flags from command line (currently statically implemented)
    page_to_crawl = BASE_URL[3]

    #create file for reporting to
    write_headers_to_file()

    #determine function to call for page crawling
    case page_to_crawl
        when BASE_URL[0]
            fetch_squirrelmail(BASE_URL[0])
        when BASE_URL[1]
            fetch_wordpress(BASE_URL[1])
        when BASE_URL[2]
            fetch_media_wiki(BASE_URL[2])
        when BASE_URL[3]
            fetch_modx(BASE_URL[3])
    end

end



##-----------------------------------------------------------------------------
## FUNCTION: fetch_squirrelmail
## PURPOSE: fetch plugins from squirrelmail and write to file
##-----------------------------------------------------------------------------
def fetch_squirrelmail(base_url)
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




##-----------------------------------------------------------------------------
## FUNCTION: fetch_media_wiki
## PURPOSE: fetch plugins from mediawiki and write to file
##-----------------------------------------------------------------------------
def fetch_media_wiki(base_url)

    #starting with the first page, loop through all plugin pages
    agent = Mechanize.new
    agent.user_agent = Repository::CHROME_USER_AGENT
    outer_page = agent.get(base_url + '/wiki/Special:AllPages/Extension:')

    error_log = ErrorLog.new

    next_page = ''

    begin
    
        current_page = next_page
        
        if(current_page != '')
            agent = Mechanize.new
            agent.user_agent = Repository::CHROME_USER_AGENT
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
                results = Array.new(4)

                results[0] = link.text #name
                results[2] = base_url + link.href #URL
                results[3] = 'N/A'

                begin
                    #create a folder for the project
                    dir_name = results[0].gsub('/', '.').force_encoding('US-ASCII')
                    if File.directory? dir_name
                        dir_name = dir_name + '_2'
                    end
                    Dir.mkdir(dir_name)
                    Dir.chdir(dir_name)
                rescue Exception => e
                    error_log.report("Directory Creation: " + dir_name +
                            "\n\t\t" + e.message)
                    next
                end

                #loop through all links in inner page to find download
                #there exist serveral types of downloads (svn, code on page, etc.)
                inner_page.links.each do |inner_link|


                    #-----------------------------------------------------------
                    # start check for subversion
                    #-----------------------------------------------------------
                    #if files are stored in a subversion repo. w/ web acces
                    if (inner_link.text =~ /svn/i || inner_link.text =~ /subversion/i) &&
                      !(inner_link.text =~ /browse svn/i || inner_link.text =~ /download from svn/i)

                        #check if the link has a protocol (external)
                        if inner_link.href =~ /:\/\//
                            svn_url = inner_link.href
                        else
                            svn_url = base_url + inner_link.href
                        end

                        Repository.retrieve_svn_files(svn_url)

                        #don't get any more code on this page if we have already
                        #collected SVN information
                        break

                    end
                    #-----------------------------------------------------------
                    # end check for subversion
                    #-----------------------------------------------------------






                    #-----------------------------------------------------------
                    # check for a git-hub accound storage
                    #-----------------------------------------------------------
                    if( inner_link.href =~ /github\.com/ )

                        #there exists a git-hub link
                        #go the account and attempt to retrive source files
                        #should the assumption be made that this is the code?

                    end
                    #-----------------------------------------------------------
                    # end check for git-hub
                    #-----------------------------------------------------------




                    #-----------------------------------------------------------
                    # check if form must be submitted to get source
                    #-----------------------------------------------------------

                    #-----------------------------------------------------------
                    # end check for form
                    #-----------------------------------------------------------





                    #-----------------------------------------------------------
                    # check if the code is written on the page
                    #-----------------------------------------------------------

                    #-----------------------------------------------------------
                    # end check for code on page
                    #-----------------------------------------------------------




                    #get the version of the plugins (if there is one)
                    #define the default if none if found
                    results[1] = 'N/A'
                    if inner_link.text =~ /last version/i
                        #there exists a version.... so get it. lol.
                        Hpricot(inner_page.body).search('b').each do |item|
                            if item.inner_text =~ /last version/i
                                item.parent.parent.parent.search('td').each do |item2|
                                    if !(item2.inner_text =~ /last version/i)
                                        results[1] = item2.inner_text
                                        break
                                    end
                                end
                            end
                        end
                    end

                end #end inner page



                #now that we're done with the project, exit the project folder
                Dir.chdir('..')

                #write results to file
                write_to_file(results)


            end

        end #end outer page

    end while next_page != current_page

end




def fetch_modx(base_url)

    agent = Mechanize.new
    agent.user_agent = Repository::CHROME_USER_AGENT

    outer_page = agent.get(base_url)

    #get an array of all the pages
    outer_page.links.each do |link|

        if link.href =~ /extras\/package\// && link.text =~ /more info/i

            inner_agent = Mechanize.new
            inner_agent.user_agent = Repository::CHROME_USER_AGENT
            inner_page = inner_agent.get('http://www.modxcms.com/' + link.href)

            results = Array.new(4)

            #set filename to empty
            results[3] = 'N/A'

            begin
                inner_page.links.each do |inner_link|

                    if inner_link.href =~ /extras\/download\//

                        #if there is a file, record it
                        #and downlaod file
                        download_page = inner_agent.get( 'http://www.modxcms.com/' + inner_link.href)
                        download_page.links.each do |dl|
                            if dl.href =~ /extras\/dl.html\?file=/
                                results[3] = inner_link.text.to_s.gsub(/^\n +/, '').chomp
                                wgetfile = File.new( 'test', 'w')#results[3], 'w')
                                dl_file = inner_agent.get(dl.href)
                                wgetfile.write( dl_file.body )
                                wgetfile.close
                                break
                            end
                        end

                    end

                end #end inner page links
            rescue Exception => e
                next
            end

            #get version
            h = Hpricot( inner_page.body )
            results[1] = h.search("//span[@class='rm-version']").inner_text

            #get name
            title = inner_page.title
            title = title.gsub('Package: ', '')
            results[0] = title.split('|')[0]

            #get URL
            results[2] = 'http://www.modxcms.com/' + link.href

            write_to_file(results)

        end #end inner page

    end #end outer page links

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
