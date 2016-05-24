# -*- coding: utf-8 -*-
import scrapy
from wd_cloth.items import GoodsItem


class A17zwdGoodsSpider(scrapy.Spider):
	name = "17zwdgoods"
	allowed_domains = ["17zwd.com"]
	start_urls = ["http://sz.17zwd.com/shop/11985.htm?item_type=onsale"]
	start_urls_all = (
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
		print 'invoke parse_page: %s' % response.url
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
			item['goodsurl'] = response.urljoin(goods.css('a::attr(href)').extract_first()).split('&')[0]
			print 'get goods info: url[%s],name[]' % (item['goodsurl'], )
			#self.parse_GoodsDetail(item)
			request = scrapy.Request(item['goodsurl'], callback=self.parse_GoodsDetail, meta={'item': item})
			print 'done invoke parse_GoodsDetail: url[%s],name[]' % (response.url,item['goodsname'] )
			yield request
			break
	
	def parse_GoodsDetail(self, response):		
		item = response.meta['item']
		item['goodsname']=response.css('div.goods-page-show-title::text').extract_first()
		print 'invoke parse_GoodsDetail: url[%s],name[%s]' % (response.url, item['goodsname'] )
		yield item
		
		