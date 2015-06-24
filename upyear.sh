#!/bin/sh

for  i in 1 2 3 4 5 6 7 8 9 10 11 12
do
app/console upload:Contable --tasks --year=$1 --month=$i Document
done



