@echo off

mysql -u root -e "CREATE DATABASE chem_stoff";
mysql -u root chem_stoff < DummyDatabase.sql