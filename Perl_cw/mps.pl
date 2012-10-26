#!/usr/bin/perl


use CGI;

my $q  = new CGI;
my %docfiles = ();#hashmap that keeps all the doc files
my %pdffiles = ();#hashap that keeps all hte pdf files
my %both = ();#hashmap that keeps the files that exist in both pdf and doc format

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

&readLog;

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

#explore funcition handles the recursion that is done throughout the directory supplied by the user
sub explore{

my($dir) = @_;

opendir(DIRE,$dir) or print "";

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
}

#this function checks what type of format the file is and and places it to the according hashmap
sub checkFileType{

my($file,$path) = @_;

	if($file =~/(\.doc)$/ || /(\.DOC)$/){#check if the extension ends with .doc or .DOC
	$file =~ s{(\.(DOC)?(doc)?)$}{};#substitute the extension with nothing
	$path = substr($path,length($maindir)+1);#cut the path supplied by the use and get the remaining
	$docfiles{$file} = $path;#add in the hashmap as key the filename and as value the path
	}
	elsif($file =~/(\.pdf)$/ || /(\.PDF)$/){#do the same as above
	$file =~ s{(\.(PDF)?(pdf)?)$}{};
	$path = substr($path,length($maindir)+1);
	$pdffiles{$file} = $path;
	}
	else{
	break;
	}	


}

#this function checks for the files on the docfiles hashmap and on the pdffiles hashmap and stores the similar file onto the both hashmap 
sub checkForBoth{

foreach(keys %docfiles){


if
$both{$_} = $docfiles{$_} if(exists $pdffiles{$_});

}

#prints the results
foreach (keys %both){

print "$both{$_}/$_<br>"; 

}


}

sub readLog{

$pdfcounter=0;
$doccounter=0;

open(FILE,$logfile) or print "error";

while(<FILE>){

	if($_=~/\.pdf/ || $_=~/\.PDF/){

	$get = substr($_,index($_,"GET")+4);
        $thePath = substr($get,1,index($get,"\.")-1);
        @line = split(/\//,$thePath);
	$pdfcounter++ if(exists $both{$line[scalar(@line)-1]})


	}
	elsif($_=~/\.doc/ || $_=~/\.DOC/){
	
	$get = substr($_,index($_,"GET")+4);
        $thePath = substr($get,1,index($get,"\.")-1);
        @line = split(/\//,$thePath);
	$doccounter++ if(exists $both{$line[scalar(@line)-1]});
	
	}

}


}
