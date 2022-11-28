#! /bin/bash

mount -t cifs //192.168.10.181/bruto/ /bruto -o username=administrador,password=Person!#@$,uid=33,gid=33
mount -t cifs //192.168.70.10/ftp/ /ftp -o username=producao,password=Person!#@$,uid=33,gid=33
