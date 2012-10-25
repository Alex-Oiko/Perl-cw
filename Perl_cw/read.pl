#!/usr/bin/perl

open(FILE,"./access.log");


while(<FILE>){

        if($_=~/\.pdf/ || $_=~/\.PDF/){

        $get = substr($_,index($_,"GET")+4);
        $thePath = substr($get,1,index($get,"\.")-1);
	@line = split(/\//,$thePath);
	#print scalar(@line)
	print "pdf is $line[scalar(@line)-1]\n";
	#$reverse = reverse($thePath);
	#print "$reverse\n";
        #$reversedfile = substr($reverse,0,index($reverse,"\/"));
	#$theFile = reverse($reversedfile);
        #print "pdf is $theFile\n";
        #$pdfcounter++ if(exists $both{$theFile})

        }
        elsif($_=~/\.doc/ || $_=~/\.DOC/){

        $get = substr($_,index($_,"GET")+4);
        $thePath = substr($get,1,index($get,"\.")-1);
	#print "$thePath\n";
	$reverse = reverse($thePath);
	#print "$reverse\n";
        $reversedfile = substr($reverse,0,index($reverse,"\/"));
        $theFile = reverse($reversedfile);
        print "doc is $theFile\n";
        #$doccounter++ if(exists $both{$theFile});
                
        }
}

sub getFileFromLog{




}


