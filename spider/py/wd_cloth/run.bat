echo off
del a.log
scrapy crawl 17zwd --logfile=a.log
echo 'crawl done'
pause
