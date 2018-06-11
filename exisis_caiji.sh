#! /bin/bash
# time 2012-10-10 
# program : 判断进行是否存在，并重新启动

count=`ps -ax|grep '/home/wwwroot' | wc -l`
#echo $count

sec=60
#开始循环，以判断程序是否关闭

for var in 1 2
do
  if [ $count -gt 1 ]; then
    #若进程还未关闭，则脚本sleep几秒   
    sleep $sec
  else
	if [ $count -gt 5 ]; then
	    exit
	fi
    #若进程已经关闭，则跳出循环
    /usr/local/php/bin/php /home/wwwroot/default/caiji/artisan iqiyi
    #/usr/local/php/bin/php /home/wwwroot/default/caiji/artisan caiji 1
    exit
  fi
done
#调用启动脚本  
nohup exists_caiji.sh &  
