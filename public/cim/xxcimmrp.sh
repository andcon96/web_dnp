#!/bin/bash
#
/usr/bin/sshpass -f /root/.pass.txt scp /opt/lampp/htdocs/web_danapaint/public/cim/xxcimmrp.cim 172.28.30.160:/mrp/.
/usr/bin/sshpass -f /root/.pass.txt ssh root@172.28.30.160 /usr/qad/batch/qadtrain/xxcimmrp/client.xxcimmrp
