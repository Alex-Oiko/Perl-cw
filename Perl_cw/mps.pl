#!/usr/bin/perl


use CGI;

my $q  = new CGI;
my %docfiles = ();#hash that keeps all the doc files
my %pdffiles = ();#hash that keeps all hte pdf files
my %both = ();#hash that keeps the files that exist in both pdf and doc format

print $q->header;
print $q->start_html("Knurled Widgets Website Tools");
print $q->h1("Knurled Widgets Website Tools");
&print_form;
&print_results;
print $q->end_html;

#creates the initial form according to the spec
sub print_form{

print $q->start_form("GET");

print $q->p("<i>Path for the datasheet hierarchy root?</i>");
print $q->textfield('path');
print $q->p("<p></p>");

print $q->p("<i>Apache Log file path name?</i>");
print $q->textfield('logfile');
print $q->p("<p></p>");

print $q->p("<i>List datasheets which occur</i>");
print $q->checkbox_group(
			-name=>'file_type',
			-values=>[in_pdf,in_doc,in_both],
			-default=>'in_both',
			-linebreak=>'yes');
print $q->p("<p></p>");

print $q->p("<i>Apache log count of pdf requests and doc requests for datasheets in both formats</i>");

print $q->radio_group(-name=>'count',
                    -values=>['yes','no'],
		    -default=>'yes',
		    -linebreak=>'yes');
print $q->p("<p></p>");

print $q->submit(-name=>'Submit',
		 -value=>'Submit');

print $q->reset(-name=>'Reset',
                 -value=>'Reset');
}

#calls the function &explore prints the results of the query, according to the options supplied by the user in the initial form
sub print_results{

$maindir = $q->param('path');
@filetypes = $q->param('file_type');
$logfile = $q->param('logfile');

&explore($maindir);

foreach (@filetypes){

if($_=~/in_pdf/){#check if the user has the in_pdf option on
	print $q->h2("List of pdf datasheets");
	&print_pdfs; 
	}
if($_=~/in_doc/){#check if the user has the in_doc option on
	print $q->h2("List of doc datasheets");
	&print_docs; 

	}

if($_=~/in_both/){#check if the user has the in_both option on
	print $q->h2("List of dual format datasheets");
	&checkForBoth;

	}
}


if($q->param('count')=~/yes/){#check if the user has the yes option on

&readLog;#this method will read the apache log supplied by the user

print $q->h2("Number of pdf requests for dual format datasheets = ",$pdfcounter);

print $q->h2("Number of doc requests for dual format datasheets = ",$doccounter);

}

}

#print all the doc files in the docfiles hashmap, along with the path
sub print_docs{

foreach (keys %docfiles){
        print "$docfiles{$_}/$_<br>";

	}
}

#print all the pdf files in the pdffiles hashmap, along with the path
sub print_pdfs{

foreach (keys %pdffiles){
        print "$pdffiles{$_}/$_<br>";
	}

}

#explore function handles the recursion that is done throughout the directory supplied by the user in order to find files and add them to hashmaps eventually 
sub explore{

my($dir) = @_;

opendir(DIRE,$dir) or print "<br>The directory you have supplied does not exist, or it is not a directory.</br>";

foreach(readdir(DIRE)){

if(-d "$dir/$_" && (/\./ || /\.\./)){#check if it is a directory. If it is this dir or the previous(. or ..) break
		break;
		}

elsif(-f "$dir/$_"){#check if it is a file and if it is call the checkFileType function
	&checkFileType($_,"$dir");
}

elsif(-d "$dir/$_"){#check if it is a directory and if it is recurse with explore on that directory
	&explore("$dir/$_",);
		}
	}
closedir(DIRE);

}

#this function checks what type of format the file is and and places it to the according hashmap
sub checkFileType{

my $lengthOfdir = length($maindir)+1;#the length of the string(path) supplied by the user. It will be used to cut the path.

my($file,$path) = @_;

	if($file =~/(\.[Dd][Oo][Cc])$/){#check if the extension ends with .doc
	$file =~ s{(\.[Dd][Oo][Cc])$}{};#substitute the extension with nothing
	$path = substr($path,$lengthOfdir);#cut the path supplied by the use and get the remaining
	$docfiles{$file} = $path;#add in the hashmap as key the filename and as value the path
	}
	elsif($file =~/(\.[Pp][Dd][Ff])$/){#do the same as above
	$file =~ s{(\.[Pp][Dd][Ff])$}{};
	$path = substr($path,$lengthOfdir);
	$pdffiles{$file} = $path;
	}
	else{
	break;
	}	


}

#this function checks for the files on the docfiles hashmap and on the pdffiles hashmap and stores the similar file onto the both hashmap 
sub checkForBoth{

#for efficiency reasons ckeck wich hashmap is bigger. This check in the event that one hashmap is a lot larger than the other one/
if(scalar[keys %docfiles]<=scalar[keys %pdffiles]){

	foreach(keys %docfiles){

	$both{$_} = $docfiles{$_} if(exists $pdffiles{$_});

	}

}
elsif(scalar[keys %docfiles]>scalar[keys %pdffiles]){

	foreach(keys %pdffiles){
	
	$both{$_} = $pdffiles{$_} if(exists $docfiles{$_});

	}

}



#prints the results
foreach (keys %both){

print "$both{$_}/$_<br>"; 

}


}

#this function reads the apache log file and extracts the file request that was done by the server. It counts the number of files taht exist in the both hash it comes across
sub readLog{

$pdfcounter=0;
$doccounter=0;

open(FILE,$logfile) or print "<br><b>The apache log file you have supplied does not exist</b></br>";

while(<FILE>){

	if($_=~/\.[Pp][Dd][Ff]/){#check if the line matches a pdf string in it
	 $get = substr($_,index($_,"GET")+4);#get the string after the GET HTTP command
	 $thePath = substr($get,1,index($get,"\.")+3);#take the string after the dot, with file extension
	 if($thePath=~/(\.[Pp][Dd][Ff])$/){#make sure that this is the correct file extension. The reason for that being that when checking for pdf or PDF in the first if statement, a random pdf might matched. Although highly unlikely that this string will match a string in the %both hash, someone might tamper with the log file and produce false data
	 	$thePath = substr($thePath,1,index($thePath,"\.")-1);#take the path without the file extension
	 	@line = split(/\//,$thePath);#split the thePath string
		$pdfcounter++ if(exists $both{$line[scalar(@line)-1]});#increment the counter if the file exists in the both hash
		}
	}
	#same as before here but with the doc files
	elsif($_=~/\.[Dd][Oo][Cc]/){
	$get = substr($_,index($_,"GET")+4);
	$thePath = substr($get,1,index($get,"\.")+3);
	if($thePath=~/(\.[Dd][Oo][Cc])$/){
		$thePath = substr($thePath,1,index($thePath,"\.")-1);
	        @line = split(/\//,$thePath);
	        $doccounter++ if(exists $both{$line[scalar(@line)-1]});
	                 }

	         }
	
	}

closedir(FILE);

}
