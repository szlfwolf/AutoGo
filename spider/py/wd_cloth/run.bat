echo off
del shop.log goods.log
scrapy crawl 17zwd --logfile=shop.log
echo 'crawl shop done'
scrapy crawl 17zwdgoods --logfile=goods.log
echo 'crawl goods done'
pause
