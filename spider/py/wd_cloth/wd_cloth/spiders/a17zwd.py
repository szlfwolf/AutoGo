# -*- coding: utf-8 -*-
import scrapy
#from scrapy import log
from wd_cloth.items import ShopItem
from wd_cloth.items import ShopInfoItem
from wd_cloth.items import CityItem
from wd_cloth.items import MarketItem
from wd_cloth.items import CategoryItem
import re

#输入【http://gz.17zwd.com/market.htm】
#抓取【城市（站点）信息】
#抓取【市场信息】 
#抓取【店铺信息】
class A17zwdSpider(scrapy.Spider):
	name = "17zwd"
	allowed_domains = ["17zwd.com"]
	start_urls = ["http://sz.17zwd.com/market.aspx"]
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
	
	cityItem_list = []
	
	
	def parse(self, response):
		listcss = response.css("div.florid-market-nav-item-right")
		curcity = ''
		#匹配城市列表
		if response.url == self.start_urls[0] :
			for index,city in enumerate(listcss[0].css("a")):
				cityurl = city.css("a::attr(href)").extract_first().split("#")[0]
				cityname = city.css("a::text").extract_first()
				cityitem = CityItem()
				cityitem['cityname'] = cityname
				cityitem['cityurl'] =  response.urljoin(cityurl)
				yield cityitem
				self.cityItem_list.append(cityitem)
				if cityitem['cityurl'] !=  self.start_urls[0] :
					#print cityitem['cityurl']
					yield scrapy.Request(cityitem['cityurl'])
				else:
					curcity=cityname
		if not curcity:
			for ci in self.cityItem_list:
				if response.url == ci['cityurl'] :
					curcity = ci['cityname']
		#匹配档口列表
		for index,market in enumerate(listcss[1].css("a")):
			#if index == 0 :
			#	continue
			if market.css("a::attr(href)").re(".*mid.*") :
				marketurl = market.css("a::attr(href)").extract_first().split("#")[0]
				marketname = market.css("a::text").extract_first()
				marketitem = MarketItem()
				marketitem['marketname'] = marketname
				marketitem['marketurl'] =  response.urljoin(marketurl)
				marketitem['cityname'] = curcity
				#print marketurl , marketname
				yield marketitem
		#匹配市场首页+市场分页
		m0=re.match(r"http://\w+\.17zwd\.com/market\.",response.url)
		#匹配市场分页
		m1=re.match(r"http://\w+\.17zwd\.com/market\.htm\?page=\d+",response.url)
		#匹配店铺所有宝贝页+分页
		m2=re.match(r"http://\w+\.17zwd\.com/shop/\d+\.htm\?item_type=onsale",response.url)
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
				#yield scrapy.Request(goodslisturl,callback=self.parse_shop)
				yield item
		elif m2:
			yield scrapy.Request(goodslisturl,callback=self.parse_shop)


	#分析市场页面内容(market.htm)
	def parse_market(self, response):
		shoplist = response.css('div.florid-ks-waterfall')
		for index,shop in enumerate(shoplist):
			item = ShopItem()
			item['shopid'] = int(shop.re("\d+\.htm")[0].split('.')[0])
			item['shopname'] = shop.css('div.florid-describing-clothes::text').extract_first()
			shopurl=response.urljoin(shop.css('a.florid-product-picture::attr(href)').extract_first())
			if shopurl:
				item['shopurl'] = shopurl.split('?')[0]
			else:
				pass
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
			item['qqinfo'] = '' #contactinfo[0]
			item['wwinfo'] = '' #contactinfo[1]
			item['props'] = shop.css('div.florid-icon-set a::attr("title")').extract()
			args = (index+1, item['shopname'], item['marketname'])
			self.log('shop info [%d]: name[%s],market[%s]'% args)
			yield item
	


