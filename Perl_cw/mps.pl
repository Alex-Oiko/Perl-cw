#!/usr/bin/perl


use CGI;

my $q  = new CGI;

print $q->header;
print $q->start_html("Knurled Widgets Website Tools");
print $q->h1("Knurled Widgets Website Tools");
&print_form;
&print_results;
print $q->end_html;

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


sub print_results{

$maindir = $q->param('path');
@filetypes = $q->param('file_type');
$logfile = $q->param('logfile');
%docfiles = ();
%pdffiles = ();
%both = ();

&explore($maindir);

foreach (@filetypes){

if($_=~/in_pdf/){
	print $q->h2("List of pdf datasheets");
	&print_pdfs; 
	}
if($_=~/in_doc/){
	print $q->h2("List of doc datasheets");
	&print_docs; 

	}

if($_=~/in_both/){
	print $q->h2("List of dual format datasheets");
	&checkForBoth;

	}
}


if($q->param('count')=~/yes/){

&readLog;

print $q->h2("Number of pdf requests for dual format datasheets = ",$pdfcounter);

print $q->h2("Number of doc requests for dual format datasheets = ",$doccounter);

}

}

sub print_docs{

foreach (keys %docfiles){
        print "$docfiles{$_}/$_<br>";

	}
}
sub print_pdfs{

foreach (keys %pdffiles){
        print "$pdffiles{$_}/$_<br>";
	}

}

sub explore{

my($dir) = @_;

opendir(DIRE,$dir) or print "";

foreach(readdir(DIRE)){

if(-d "$dir/$_" && (/\./ || /\.\./)){
#do nothing
		}

elsif(-f "$dir/$_"){
	&checkFileType($_,"$dir");
}

elsif(-d "$dir/$_"){
	&explore("$dir/$_",);
		}
	}
}

sub checkFileType{

my($file,$path) = @_;

	if($file =~/(\.doc)$/ || /(\.DOC)$/){
	$file =~ s{(\.([A-Z][A-Z][A-Z])?([a-z][a-z][a-z])?)$}{};
	$path = substr($path,length($maindir)+1);
	$docfiles{$file} = $path;
	}
	elsif($file =~/(\.pdf)$/ || /(\.PDF)$/){
	$file =~ s{(\.([A-Z][A-Z][A-Z])?([a-z][a-z][a-z])?)$}{};
	$path = substr($path,length($maindir)+1);
	$pdffiles{$file} = $path;
	}
	else{
	print "BANANAS";
	}	


}

sub checkForBoth{

foreach(keys %docfiles){

#push @both,$_ if (exists $pdffiles{$_});
$both{$_} = $docfiles{$_} if(exists $pdffiles{$_});

}

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
