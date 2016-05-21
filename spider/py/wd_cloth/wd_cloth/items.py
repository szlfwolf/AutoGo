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
	marketname= scrapy.Field()
	marketfloor= scrapy.Field()
	marketdk= scrapy.Field()
	category= scrapy.Field()
	tip= scrapy.Field()
	qqinfo= scrapy.Field()
	wwinfo= scrapy.Field()
	props= scrapy.Field()
