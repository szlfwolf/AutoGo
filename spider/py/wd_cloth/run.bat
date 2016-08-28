echo off
del shop.log goods.log
scrapy crawl 17zwd --logfile=shop.log
echo 'crawl shop done'
rem 指定地址【店铺地址/商品地址】抓取商品信息
rem F:\Felix\soho\AutoGo\spider\py\wd_cloth>scrapy crawl 17zwdgoods -a url="http://sz.17zwd.com/shop/14171.htm"
scrapy crawl 17zwdgoods --logfile=goods.log
echo 'crawl goods done'
pause
