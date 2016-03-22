#!/usr/bin/env bash

echo -n > new.csv
echo -n > old.csv

for i in `seq 1 100`; do
	curl -s "http://vagrant.local/measure-rewrites/?rewrite=new" >> new.csv
	curl -s "http://vagrant.local/measure-rewrites/?rewrite=old" >> old.csv
done
