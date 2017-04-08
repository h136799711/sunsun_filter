#!/bin/sh
source /etc/profile
#git仓库路径
function gitpull(){
    for file in ` ls $1 `
    do
        if [ -e $1"/"$file ]
        then
            pull=$1"/"$file
            echo $pull
        fi
    done
}
INIT_PATH="/home/git"
gitpull $INIT_PATH
