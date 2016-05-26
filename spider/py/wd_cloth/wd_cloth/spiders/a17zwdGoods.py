# -*- coding: utf-8 -*-
import scrapy
from wd_cloth.items import GoodsItem
import re
import json


class A17zwdGoodsSpider(scrapy.Spider):
	name = "17zwdgoods"
	allowed_domains = ["17zwd.com"]
	start_urls_tmp = ["http://sz.17zwd.com/shop/11985.htm?item_type=onsale"]
	start_urls = (
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
	)
	#def start_requests(self):
		#return ['http://sz.17zwd.com/shop/11985.htm?item_type=onsale']
		
	def parse(self, response):
		self.parse_page(response)
		for request in self.parse_goodslist(response):
			yield request
		
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
		print 'invoke parse_goodslist: %s' % response.url
		goodslist = response.css('div.florid-shop-goods-item')
		item=[]
		for index,goods in enumerate(goodslist):
			item = GoodsItem()
			item['shopurl'] = response.url.split("?")[0]
			item['goodsurl'] = response.urljoin(goods.css('a::attr(href)').extract_first()).split('&')[0]
			request = scrapy.Request(item['goodsurl'], callback=self.parse_GoodsDetail, meta={'item': item})
			yield request
			break
	
	def parse_GoodsDetail(self, response):
		item = response.meta['item']
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
