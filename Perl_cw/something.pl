#!/usr/bin/perl
use CGI;

my $q = new CGI;

print $q->header;

print $q->start_html;
print $q->h1("Hello world");
print $q->p("malakas");
print $q->end_html;
