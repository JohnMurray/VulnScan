require 'Mechanize'
require 'fileutils'
require 'hpricot'


module Repository

    CHROME_USER_AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5'

    ##------------------------------------------------------------------------------
    ## FUNCTION: retrieve_svn_files
    ## PURPOSE: retrive files from a web-svn repository (non-recursive)
    ##
    ## @TODO: add in some type of error reporting mechanism
    ## @TODO: make svn scanning recursive (follow folder links)
    ## @TODO: fix to not download folders as a file
    ##------------------------------------------------------------------------------
    def Repository.retrieve_svn_files(url)

        #create a virtual browser with a Chrome Windows agent
        agent = Mechanize.new
        agent.user_agent = CHROME_USER_AGENT

        #retrive the page and report if page not found (404)
        begin
            page = agent.get(url)
        rescue Exception => e
            #REPORT THE ERROR
            return
        end


        #iterate through the svn page links, downloading all files
        page.links.each do |svn_link|
            if !(svn_link.text == '..' || svn_link.text =~ /^subversion$/i)

                #prepare link
                svn_file_link = svn_link.text
                if !(svn_link.href =~ /:\/\//)
                    svn_file_link = url + svn_link.href
                end

                #attempt to download the file
                begin
                    svn_file = File.new(svn_link.text, 'w')
                    svn_file.write(agent.get(svn_file_link).body)
                    svn_file.close
                rescue Exception => e
                    #REPORT THE ERROR
                end
            end

        end

    end



    ##--------------------------------------------------------------------------
    ## FUNCTION: retrieve_github_files
    ## PURPOSE: retrieve files from github.com
    ##
    ## @TODO:
    ##--------------------------------------------------------------------------
    def retrieve_github_files(url)

        #create a virtual browser with a Chrome Windows Agent
        agent = Mechanize.new
        agent.user_agent = CHROME_USER_AGENT

        #retrieve the page and report if page not found (404)
        begin
            page = agent.get(url)
        rescue Exception => e
            #REPORT THE ERROR
        end

        #recursively download all content
        get_files_from_github_page(page)

    end


    ##--------------------------------------------------------------------------
    ## FUNCTION: get_files_from_gethub_page
    ## PURPOSE: download all files given a starting page on github
    ## PARAMETERS: Mechanize::Page page
    ##
    ## NOTE: funciton is recursive in nature
    ##
    ## @TODO:
    ##--------------------------------------------------------------------------
    def get_files_from_github_page(page)

        #determine if we are on a file page or a listing page (or both)

        #load the text and search for the "browser" div
        html = Hpricot(page.body)
        browser_div = html.search("//div[@id='browser']")

        #check if their is no browser on teh page
        if browser_div.size == 0

        end

    end


end



class ErrorLog

    ERROR_LOG = 'errors.log'

    def initialize()
        @fh = File.new(ERROR_LOG, 'w')
    end

    def report(error)
        @fh.write(error.to_s + "\n\n")
    end

    def print_errors_to_screen()
        rfh = File.new(ERROR_LOG, 'a')
        rfh.each do |line|
            puts line
        end
    end

    def close()
        @fh.close()
    end

end