# -*- coding: utf-8 -*-
import scrapy
from scrapy import log
from wd_cloth.items import ShopItem


class A17zwdGoodsSpider(scrapy.Spider):
	name = "17zwdGoods"
	allowed_domains = ["17zwd.com"]
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
	def parse_page(self, response):
		print 'get pageinfo'
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
		else :
			print 'no page found'
			self.log('no pager fond!')
		
	
	def parse(self, response):
		#filename = response.url.split("/")[-2]
		#fs=open(filename, 'ab')
		if response.url.endswith('htm'):
			print 'invoke parse_page'
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
			contactinfo = shop.css('a.florid-arRight::attr(href)').extract()
			item['qqinfo'] = contactinfo[0]
			item['wwinfo'] = contactinfo[1]
			item['props'] = shop.css('div.florid-icon-set a::attr("title")').extract()
			args = (index+1, item['shopname'], item['marketname'])
			self.log('shop info [%d]: name[%s],market[%s]'% args)
			yield item