# -*- coding: utf-8 -*-
import scrapy
from wd_cloth.items import GoodsItem
from wd_cloth.items import ShopInfoItem
import re
import json
from wd_cloth.dbcontext import dbcontext
import time

#输入【从db读取的店铺地址】
#抓取【店铺信息】
#抓取【商品列表信息】
#抓取【商品信息】
class A17zwdGoodsSpider(scrapy.Spider):
	name = "17zwdgoods"
	allowed_domains = ["17zwd.com"]
	#start_urls = ["http://sz.17zwd.com/shop/11985.htm?item_type=onsale"]
	
	def start_requests(self):
		#start_urls = ["http://gz.17zwd.com/shop/27797.htm?item_type=onsale"]
		dbc = dbcontext()
		for url in dbc.getshops():
		#for url in start_urls:
			#time.sleep(2)
			yield self.make_requests_from_url(url)
		
	def parse(self, response):
		self.parse_page(response)
		for request in self.parse_goodslist(response):
			yield request
		#分析店铺页面的店铺信息
		for h3 in range(1,2):
			item = ShopInfoItem()
			item['shopid'] = int(re.match(".*\/(\d+)\.htm.*",response.url).groups()[0])
			item['shopinfourl']=response.url.split('?')[0]
			#item['shopimg'] = response.css('div.figure-image img::attr(src)').extract()
			#QQ
			#淘宝
			#电话
			contactlist = response.css('div.figure-server-right a::text').extract()
			if len(contactlist) == 3:
				item['qqnum'] = contactlist[0]
				item['wwname'] = contactlist[1]
				item['phonenum'] = contactlist[2].strip()
			elif len(contactlist) == 2:
				#qq可能为空
				item['qqnum'] = ''
				item['wwname'] = contactlist[0]
				item['phonenum'] = contactlist[1].strip()
			
			#淘宝店地址
			item['tburl']="http:" +response.css('div.florid-goods-details-taobao-enter a::attr(href)').extract_first()
			
			yield item
		
	def parse_page(self, response):
		print 'do parse_page: %s' % response.url
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
					request = scrapy.Request(url,callback=self.parse_goodslist)
	
	def parse_goodslist(self, response):
		shopid =int(re.match(".*\/(\d+)\.htm.*",response.url).groups()[0])
		goodslist = response.css('div.florid-shop-goods-item')
		for index,goods in enumerate(goodslist):
			item = GoodsItem()
			item['shopid'] = shopid
			item['shopurl'] = response.url.split("?")[0]
			item['goodsurl'] = response.urljoin(goods.css('a::attr(href)').extract_first()).split('&')[0]
			request = scrapy.Request(item['goodsurl'], callback=self.parse_GoodsDetail, meta={'item': item})
			yield request
	
	def parse_GoodsDetail(self, response):
		item = response.meta['item']
		item['goodsid'] = int(re.match(".*GID=(\d+)",response.url).groups()[0])
		item['goodsname']=response.css('div.goods-page-show-title::text').extract_first()		
		item['uptime']=response.css('a.parameter-item-show::text').extract_first()
		item['taobaourl'] = response.css('a.gototb-car::attr(href)').extract_first()
		item['goodsprice'] = float(response.css('span.goods-price i::text').re("\d+\.?\d+")[0])
		item['taobaoprice'] = float(response.css('span.goods-taobao-price del::text').re("\d+\.?\d+")[0])
		#主图列表(50*50)=>(400*400)
		item['goodsimgs'] = json.dumps(response.css("div.goods-page-small-container img::attr(src)").extract())
		#下一步从details中抓取图片
		item['details'] = response.css("div.details-right-allTB-image-container").extract_first()
		#获取商品属性
		x= response.css("script").re("gAttribute='([^']*)'")[0]
		props={}
		for x1 in x.split(u"|"):
			x2=x1.split(u"：")
			#print "k:[%s] v:[%s]" % (x2[0] ,x2[1])
			if x2[0] in props:
				props[x2[0]] = props[x2[0]] +","+ x2[1]
			else: 
				props[x2[0]] = x2[1]
		item['props']=json.dumps(props,ensure_ascii=False)
		yield item

	#分析商店页面内容(shop/12345.htm)
	def parse_shop(self,response):
		print 'parse_shop' , response.url
		item = ShopInfoItem()
		item['shopid'] = int(re.match(".*\/(\d+)\.htm.*",response.url).groups()[0])
		item['shopinfourl']=response.url.split('?')[0]
		#item['shopimg'] = response.css('div.figure-image img::attr(src)').extract()
		#QQ
		#淘宝
		#电话
		contactlist = response.css('div.figure-server-right a::text').extract()
		if len(contactlist) == 3:
			item['qqnum'] = contactlist[0]
			item['wwname'] = contactlist[1]
			item['phonenum'] = contactlist[2].strip()
		elif len(contactlist) == 2:
			#qq可能为空
			item['qqnum'] = ''
			item['wwname'] = contactlist[0]
			item['phonenum'] = contactlist[1].strip()
		
		#淘宝店地址
		item['tburl']="http:" +response.css('div.florid-goods-details-taobao-enter a::attr(href)').extract_first()
		
		yield item
	