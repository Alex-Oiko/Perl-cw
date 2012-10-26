#i/usr/bin/perl 
##
 asdasdasdasd

        use CGI ':standard';

        print header;    # from CGI module sends a standard HTTP header
        print start_html("Example CGI.pm Form"); #from CGI sends the top of the web page
        print "<h1> Example CGI.pm Form</h1>\n";
        print_prompt();
        do_work();
        print_tail();
        print end_html; # CGI: sends the end of the html

        sub print_prompt {
 			$method="GET"; 
 			
 			      
           print start_form($method);
           print "<em>What's your name?</em><br>";
           print textfield('name');
           print checkbox('Not my real name');

           print "<p><em>Where can you find English Sparrows?</em><br>";
           print checkbox_group(
                                 -name=>'Sparrow locations',
                                 -values=>[England,France,Spain,Asia,Hoboken],
                                 -linebreak=>'yes',
                                 -defaults=>[England,Asia]);

           print "<p><em>How far can they fly?</em><br>",
                radio_group(
                        -name=>'how far',
                        -values=>['10 ft','1 mile','10 miles','real far'],
                        -default=>'1 mile');

           print "<p><em>What's your favorite color?</em>  ";
           print popup_menu(-name=>'Color',
                                    -values=>['black','brown','red','yellow'],
                                    -default=>'red');


           print "<p><em>What have you got there?</em><br>";
           print scrolling_list(
                         -name=>'possessions',
                         -values=>['A Coconut','A Grail','An Icon',
                                   'A Sword','A Ticket'],
                         -size=>5,
                         -multiple=>'true');

           print "<p><em>Any parting comments?</em><br>";
           print textarea(-name=>'Comments',
                                  -rows=>10,
                                  -columns=>50);

           print "<p>",reset;
           print submit('Action','Submit');
          
           print end_form;
           print "<hr>\n";
        }

        sub do_work {

           
          
           
           print "<h2>Here are the current settings in this form</h2>";

           for my $key (param) { 
              print "<strong>$key</strong> -> ";
              my @values = param($key);
              print join(", ",@values),"<br>\n";
          }
          $query=$ENV{'QUERY_STRING'}; # ok using GET
          print "This is the query string   ", $query;
        }

        sub print_tail {
# print <<END;
 #      		<hr>
 #      		<address>ECS</address><br>
  #     		#<a href="/">Home Page</a>
    # 		END
        }

