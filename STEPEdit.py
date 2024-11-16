#!/usr/bin/env python   

"""A little script to prefix the ID and name attribute of "product" entities in STEP files.

Copyright: (C) 2015,2024 Michael Bemmerl
License: MIT License

Requirements:
- Python >= 3.8 (well, obviously ;-)

Tested with Python 3.8.2
"""

import re
import argparse

parser = argparse.ArgumentParser(description="Make simple edits to STEP files.")
parser.add_argument('file', help="Path to STEP file")
parser.add_argument('--prefixProductId', help="Add prefix to 'id' of entity 'product'")
parser.add_argument('--prefixProductName', help="Add prefix to 'name' of entity 'product'")
parser.add_argument('--verbose', action='store_true', default=False, help="A little more verbose output")

args = parser.parse_args()

class STEPEdit(object):
    
    def __init__(self, filename, verbose = False):
        self.filename = filename
        self.verbose = verbose
        self.changed = None
        
        # Load file contents into memory
        with open(self.filename, 'r') as file:
            self.data = file.readlines()
        
    def save(self):
        if self.changed is not None:
            with open(self.filename, 'w') as file:
                file.writelines(self.data)

    def prefix_product_id(self, prefix):
        # #7 = PRODUCT('as1','as1','',(#8));
        # Id is the first attribute
        regex = re.compile("(^.*PRODUCT\(')(.*?)('.*\);$)")
        for index, line in enumerate(self.data):
            match = regex.search(line)
            if match is not None:
                self.changed = True
                self.data[index] = "{0}{1}{2}{3}\n".format(match.group(1), prefix, match.group(2), match.group(3))
                self.print_change(match.group(2), prefix + match.group(2), line, self.data[index])
    
    def prefix_product_name(self, prefix):
        # #7 = PRODUCT('as1','as1','',(#8));
        # Name is the second attribute
        regex = re.compile("(^.*PRODUCT\('.*?',')(.*?)(',.+\);.*$)")
        for index, line in enumerate(self.data):
            match = regex.search(line)
            if match is not None:
                self.changed = True
                self.data[index] = "{0}{1}{2}{3}\n".format(match.group(1), prefix, match.group(2), match.group(3))
                self.print_change(match.group(2), prefix + match.group(2), line, self.data[index])
                
    def print_change(self, old, new, line, newline):
        if self.verbose:
            print("Changed line \"{0}\" to \"{1}\"".format(line.strip(), newline.strip()))
        else:
            print("Changed \"{0}\" to \"{1}\"".format(old, new))
        
# Instantiate class with arguments from the command line
instance = STEPEdit(args.file, args.verbose)

if args.prefixProductId is not None:
    instance.prefix_product_id(args.prefixProductId)
if args.prefixProductName is not None:
    instance.prefix_product_name(args.prefixProductName)
    
instance.save()

if instance.changed is None:
    print("No changes were made to the file.")
