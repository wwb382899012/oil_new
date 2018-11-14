#!/bin/sh

case $1 in
    'start' )
        echo "Starting ...."
        nohup ./modc email 10000000 >/dev/null 2>&1 &
        nohup ./modc oillog 10000000 >/dev/null 2>&1 &
        nohup ./modc remind 10000000 >/dev/null 2>&1 &
        nohup ./modc file 10000000 >/dev/null 2>&1 &
        nohup ./modc block 10000000 >/dev/null 2>&1 &
        nohup ./modc reportMq 10000000 >/dev/null 2>&1 &
        nohup ./modc payment 10000000 >/dev/null 2>&1 &
        nohup ./modc price 10000000 >/dev/null 2>&1 &
        nohup ./modc quantity 10000000 >/dev/null 2>&1 &
        echo "Start end .... "
        ;;

    'stop' )
        echo "Stopping .... "

        kill `ps -ef | egrep 'email 10000000|oillog 10000000|remind 10000000|file 10000000|block 10000000|payment 10000000|reportMq 10000000|price 10000000|quantity 10000000'| grep -v egrep | awk '{print $2}'`

        echo "Stop end .... "
        ;;      

    'restart'|'reload' )
        ${0} stop
        ${0} start
        ;;      

    'list' )

        ps -ef | egrep 'email 10000000|oillog 10000000|remind 10000000|file 10000000|block 10000000|payment 10000000|reportMq 10000000|price 10000000|quantity 10000000'| grep -v egrep

        ;;      

    *)
echo "usage: `basename $0` {start|restart|stop|list}"
esac
