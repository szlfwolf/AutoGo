# -*- coding: utf-8 -*-
import scrapy
#from scrapy import log
from wd_cloth.items import ShopItem
from wd_cloth.items import ShopInfoItem
import re


class A17zwdSpider(scrapy.Spider):
	name = "17zwd"
	allowed_domains = ["17zwd.com"]
	start_urls = ["http://sz.17zwd.com/market.htm"]
	start_urls_test = {
		'http://gz.17zwd.com/market.htm',
		'http://hz.17zwd.com/market.htm',
		'http://cs.17zwd.com/market.htm',
		'http://jy.17zwd.com/market.htm',
		'http://sz.17zwd.com/market.htm',
		'http://zz.17zwd.com/market.htm',
		'http://zhengzhou.17zwd.com/market.htm',
		'http://xintang.17zwd.com/market.htm',
		'http://bj.17zwd.com/market.htm',
		'http://dg.17zwd.com/market.htm',
		'http://sz.17zwd.com/market.htm?zdid=48&mid=679'
	}
	
		
	
	def parse(self, response):
		#匹配市场首页+市场分页
		m0=re.match(r"http://\w+\.17zwd\.com/market\.htm",response.url)
		#匹配市场分页
		m1=re.match(r"http://\w+\.17zwd\.com/market\.htm\?page=\d+",response.url)
		#匹配店铺所有宝贝页+分页
		#m2=re.match(r"http://\w+\.17zwd\.com/shop/\d+\.htm\?item_type=onsale",response.url)
		#匹配店铺所有宝贝页分页
		#m3=re.match(r"http://\w+\.17zwd\.com/shop/\d+\.htm\?item_type=onsale?page=\d+",response.url)
		#匹配商品页
		#m4=re.match(r"http://\w+\.17zwd\.com/item\.htm\?GID=\d+",response.url)
		
		if m0:
			if not m1:
				#self.parse_page(response)
				pageinfo = response.css('div.pageing').re('ZWDPager\(\'_pager\', (\d*), (\d*)\)')
				if ( len(pageinfo) == 2 ) :
					pagesize = int(pageinfo[0])
					pagetotal = int(pageinfo[1])
					pagecount = pagetotal / pagesize 
					if ( pagetotal % pagesize != 0) : 
						pagecount = pagecount+1
					for i in range(pagecount):
						if i > 0 :
							page=i+1
							url = response.urljoin('?page=%d' % page)
							print 'get page:%s'% url
							yield scrapy.Request(url,callback=self.parse)
			
			#分析市场信息，获取商店信息,获取商品列表url
			for item in self.parse_market(response):
				goodslisturl = item['shopurl'] + "?item_type=onsale"
				#获取商店页面信息。
				yield scrapy.Request(goodslisturl,callback=self.parse_shop)
				yield item


	#分析市场页面内容(market.htm)
	def parse_market(self, response):
		shoplist = response.css('div.florid-ks-waterfall')
		for index,shop in enumerate(shoplist):
			item = ShopItem()
			item['shopname'] = shop.css('div.florid-describing-clothes::text').extract_first()
			item['shopurl'] = response.urljoin(shop.css('a.florid-product-picture::attr(href)').extract_first())
			marketinfo = shop.css('span.florid-arch-infor-block-font::text').extract()
			item['marketname'] = marketinfo[0]
			item['marketfloor'] = marketinfo[1]
			item['marketdk'] = marketinfo[2]
			if len(marketinfo) == 5 :
				item['category'] = marketinfo[3]
				item['tip'] = marketinfo[4]
			else:
				item['category'] = ''
				item['tip'] = ''
			contactinfo = shop.css('a.florid-arRight::attr(href)').extract()
			item['qqinfo'] = contactinfo[0]
			item['wwinfo'] = contactinfo[1]
			item['props'] = shop.css('div.florid-icon-set a::attr("title")').extract()
			args = (index+1, item['shopname'], item['marketname'])
			self.log('shop info [%d]: name[%s],market[%s]'% args)
			yield item
	
	#分析商店页面内容(shop/12345.htm)
	def parse_shop(self,response):
		#print 'parse_shop: %s' % response.url
		item = ShopInfoItem()
		item['shopinfourl']=response.url.split('?')[0]
		#QQ
		#淘宝
		#电话
		contactlist = response.css('div.figure-server-right a::text').extract()
		if len(contactlist) == 3:
			item['qqnum'] = contactlist[0]
			item['wwname'] = contactlist[1]
			item['phonenum'] = contactlist[2].strip()
		
		#淘宝店地址
		item['tburl']="http:" +response.css('div.florid-goods-details-taobao-enter a::attr(href)').extract_first()
		
		yield item
	
	#分析商店页面内容(shop/12345.htm)，抓取商品信息
	def parse_goodslist(self,response):
		goodsurllist = response.css('div.florid-shop-link a::attr(href)').extract()
		for a in goodsurllist:
			b = a.split('&')
			goodsurl = response.urljoin(b[0])
			#抓取商品信息页面
			yield scrapy.Request(goodsurl,callback=self.parse_goods) 
		
	#分析商品页面内容(item.htm?gid=12345)
	def parse_goods(self,response):
		print 'get goods page: %s' % response.url
		item = GoodsItem()
		return item

