# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy

class CityItem(scrapy.Item):
	cid = scrapy.Field() #城市(站点)id
	cityname = scrapy.Field() #城市名称
	cityurl = scrapy.Field() #城市地址
	
class MarketItem(scrapy.Item):
	mid = scrapy.Field() #市场id
	#cid = scrapy.Field() #市场所属的城市(站点)id
	marketname = scrapy.Field() #市场名称
	marketurl = scrapy.Field() #市场地址
	cityname = scrapy.Field() #城市名称

class CategoryItem(scrapy.Item):
	categoryname = scrapy.Field() #主营名称
	

class ShopItem(scrapy.Item):
	# define the fields for your item here like:
	shopid = scrapy.Field() #店铺id
	shopname = scrapy.Field() #店铺名称
	shopurl = scrapy.Field() #店铺地址
	marketname= scrapy.Field() #市场名称
	marketfloor= scrapy.Field() #市场楼层
	marketdk= scrapy.Field() #市场档口
	category= scrapy.Field() #主营列表
	tip= scrapy.Field() #优惠信息
	qqinfo= scrapy.Field() #与qqnum重复，忽略
	wwinfo= scrapy.Field() #与wwname重复，忽略
	props= scrapy.Field() #特色服务（一件代发、包换款等）
	
class ShopInfoItem(scrapy.Item):
	# define the fields for your item here like:
	shopid = scrapy.Field() #店铺id
	shopimg = scrapy.Field() #店铺图片，暂不抓取
	shopinfourl = scrapy.Field()
	qqnum = scrapy.Field() #qq号码
	wwname= scrapy.Field() #旺旺名称
	phonenum= scrapy.Field() #电话号码
	tburl= scrapy.Field() #淘宝店铺地址
	
class GoodsItem(scrapy.Item):
	# define the fields for your item here like:
	goodsid = scrapy.Field() #商品id
	shopid = scrapy.Field() #店铺id
	goodsname = scrapy.Field() #商品名称
	goodsimgs = scrapy.Field() #商铺图片列表
	goodsurl = scrapy.Field() #商品地址
	shopurl = scrapy.Field() #店铺地址，忽略
	goodsprice =scrapy.Field() #商品批发价格
	taobaoprice = scrapy.Field() #商品淘宝价格
	uptime = scrapy.Field() #上架时间
	taobaourl=scrapy.Field() #商品淘宝地址
	props = scrapy.Field() #商品属性
	details = scrapy.Field() #商品详细信息
	
