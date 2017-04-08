#!/bin/sh
chmod +x /home/wwwroot/testdevsunsun/git_update.sh
cd /home/wwwroot/testdevsunsun/
sh /home/wwwroot/testdevsunsun/git_update.sh
#git仓库路径
gitpull() {
    for file in ` ls $1 `
    do
        if [ -e $1"/"$file"/git_update.sh" ]
        then
            gitdir=$1"/"$file
            gitsh=$gitdir"/git_update.sh"
            chmod +x $gitsh
            cd $gitdir
            echo "exec start"$gitsh
            sh $gitsh
            echo "=====^_^=^_^=========="
        fi
    done
}
INIT_PATH="/home/git"
gitpull $INIT_PATH

#加热棒监控
chmod +x /home/git/itboye_sunsun_tcp/sunsun/bash/heating_rod/monitor.sh
cd /home/git/itboye_sunsun_tcp/sunsun/bash/heating_rod
sh /home/git/itboye_sunsun_tcp/sunsun/bash/heating_rod/monitor.sh

#过滤桶监控
chmod +x /home/git/itboye_sunsun_tcp/sunsun/bash/filter_vat/monitor.sh
cd /home/git/itboye_sunsun_tcp/sunsun/bash/filter_vat
sh /home/git/itboye_sunsun_tcp/sunsun/bash/filter_vat/monitor.sh


exit