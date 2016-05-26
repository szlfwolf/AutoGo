# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class ShopItem(scrapy.Item):
	# define the fields for your item here like:
	shopname = scrapy.Field()
	shopurl = scrapy.Field()
	shopimg = scrapy.Field()
	marketname= scrapy.Field()
	marketfloor= scrapy.Field()
	marketdk= scrapy.Field()
	category= scrapy.Field()
	tip= scrapy.Field()
	qqinfo= scrapy.Field()
	wwinfo= scrapy.Field()
	props= scrapy.Field()
	
class ShopInfoItem(scrapy.Item):
	# define the fields for your item here like:
	shopinfourl = scrapy.Field()
	qqnum = scrapy.Field()
	wwname= scrapy.Field()
	phonenum= scrapy.Field()
	tburl= scrapy.Field()
	
class GoodsItem(scrapy.Item):
	# define the fields for your item here like:
	goodsname = scrapy.Field()
	goodsimgs = scrapy.Field()
	goodsurl = scrapy.Field()
	shopurl = scrapy.Field()
	goodsprice =scrapy.Field()
	taobaoprice = scrapy.Field()
	uptime = scrapy.Field()
	taobaourl=scrapy.Field()
	props = scrapy.Field()
	details = scrapy.Field()
	
