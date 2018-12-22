#!/bin/bash

ceph pg dump 2>&1 | awk '
BEGIN { IGNORECASE = 1 }
 /^PG_STAT/ { col=1; while($col!="UP") {col++}; col++ }
 /^[0-9a-f]+\.[0-9a-f]+/ { match($0,/^[0-9a-f]+/); pool=substr($0, RSTART, RLENGTH); poollist[pool]=0;
 up=$col; i=0; RSTART=0; RLENGTH=0; delete osds; while(match(up,/[0-9]+/)>0) { osds[++i]=substr(up,RSTART,RLENGTH); up = substr(up, RSTART+RLENGTH) }
 for(i in osds) {array[osds[i],pool]++; osdlist[osds[i]];}
}
END {
 cnt=0;
 printf("{");
 printf("\"osd_pg_state\":{");
 for (i in osdlist) { cnt++; printf("\"osd_%i\":{", i); sum=0;
   for (j in poollist) { printf("\"pool_%s\":%i,", j, array[i,j]); sum+=array[i,j]; sumpool[j]+=array[i,j] }; printf("\"total\":%i}",sum); if (length(osdlist)!=cnt) printf(",") }
 printf("},")
 printf("\"total_pgs_pool\":{");
 cnt=0;
 for (i in poollist) { cnt++; printf("\"%s\":%s", i, sumpool[i]); if (length(poollist)!=cnt) printf(",") };
 printf("}}\n")
}'
