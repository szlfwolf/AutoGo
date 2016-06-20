# -*- coding: utf-8 -*-
import scrapy
import time
from twisted.enterprise import adbapi
from twisted.python import log
import MySQLdb
import MySQLdb.cursors
import json
import sys
from wd_cloth.items import ShopItem
from wd_cloth.items import ShopInfoItem
from wd_cloth.items import CityItem
from wd_cloth.items import MarketItem
#from wd_cloth.items import CategoryItem
from wd_cloth.items import GoodsItem



class WdClothPipeline(object):

	def __init__(self):
		try:
			conn=MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth2',port=3306)
			cur=conn.cursor()
			cur.execute('select count(*) from s_shopinfo')
			cur.close()
			conn.close()
			print "mysql connect db[wd_cloth2]ok "
		except MySQLdb.Error,e:
			print "Mysql Error %d: %s" % (e.args[0], e.args[1])
		
		#log.startLogging(sys.stdout)
		self.dbpool = adbapi.ConnectionPool('MySQLdb',
			host = '127.0.0.1',
			db = 'wd_cloth2',
			user = 'root',
			passwd = '',
			cursorclass = MySQLdb.cursors.DictCursor,
			charset = 'utf8',
			use_unicode = True
		)
		
		

	def process_item(self, item, spider):
		query = 0
		if isinstance(item,CityItem):
			#print 'CityItem: ', item['cityname'],item['cityurl']
			query = self.dbpool.runInteraction(self._insert_city, item)
		elif isinstance(item,MarketItem):
			#print 'MarketItem: ',item['cityname'],'-',item['marketname'],'-',item['marketurl']
			query = self.dbpool.runInteraction(self._insert_market, item)
		elif isinstance(item,ShopItem):
			query = self.dbpool.runInteraction(self._conditional_insert, item)
		elif isinstance(item,ShopInfoItem):
			print 'begin update shop info'
			query = self.dbpool.runInteraction(self._conditional_update_shopinfo, item)
		elif isinstance(item,GoodsItem):
			query = self.dbpool.runInteraction(self._conditional_insert_goods, item)
		
		if query:
			query.addErrback(self.handle_error)
		return item

	def handle_error(self, e):
		print 'error: %s' % e
	
	#保存【city】信息
	def _insert_city(self,tx,item):
		tx.execute("select * from s_city where cityurl = %s", (item['cityurl']))
		result = tx.fetchone()
		if not result:
			tx.execute("insert s_city (cityname,cityurl) values (%s,%s) ", ( item['cityname'],item['cityurl']))
			print 'city:[%s] added!' % item['cityname'] 
	
	#保存【市场】信息
	def _insert_market(self,tx,item):
		tx.execute("select * from s_citymarket where marketurl=%s", (item['marketurl']))
		result = tx.fetchone()
		if not result:
			tx.execute("insert s_citymarket (marketurl,marketname,cityname) values (%s,%s,%s) ", ( item['marketurl'],item['marketname'],item['cityname']))
			print 'market:[%s] added!' % item['marketname']

	#保存【店铺】信息，解析列表页
	def _conditional_insert(self, tx, item):
		tx.execute("select * from s_shopinfo where shopid = %s", (item['shopid'] ))
		result = tx.fetchone()
		if not result:
			tx.execute(\
				"insert into s_shopinfo (shopname,shopid,shopurl,marketname,marketfloor,marketdk,category,tip,qqinfo,wwinfo,props)\
				values (%s,%s,%s,%s,%s, %s,%s,%s,%s,%s,%s)",
				(item['shopname'].encode('utf-8'),
				 item['shopid'],
				 item['shopurl'],
				 item['marketname'].encode('utf-8'),
				 item['marketfloor'].encode('utf-8'),
				 item['marketdk'].encode('utf-8'),
				 item['category'].encode('utf-8'),
				 item['tip'].encode('utf-8'),
				 item['qqinfo'].encode('utf-8'),
				 item['wwinfo'].encode('utf-8'),
				 json.dumps(item['props'],ensure_ascii=False)
				 )
				)
			sid = tx.connection.insert_id()
			#保存【市场】与【店铺】的关联关系
			tx.execute("select * from s_shopinfo where shopid = %s", (item['shopid'] ))
			result = tx.fetchone()
			if result:
				marketid = int(result['id'])
				tx.execute("insert into s_market_shop (marketid,shopid) values (%s,%s)", (marketid,sid))
			print "insert s_shopinfo[%s]: done, shopid:%s" % (sid,item['shopid'])
			tx.execute("insert into s_spiderlog (optype,keyid,objname) values (%s,%s,%s)",("add",sid,"s_shopinfo"))
			

	#更新【店铺】信息，解析店铺页
	def _conditional_update_shopinfo(self, tx, item):
		tx.execute("select * from s_shopinfo where shopid = %s", (item['shopid'] ))
		result = tx.fetchone()
		if result:
			if not result['wwname']:
				tx.execute(\
					"update s_shopinfo set qqnum=%s,wwname=%s,phonenum=%s,tburl=%s where shopurl = %s",
					(item['qqnum'],item['wwname'].encode('utf-8'),item['phonenum'],item['tburl'],item['shopinfourl'])
					)
				shopid = result['id']
				print "update s_shopinfo[%s]: done." % shopid
				tx.execute("insert into s_spiderlog (optype,keyid,objname) values (%s,%s,%s)",("update",shopid,"s_shopinfo"))


	#保存【商品】信息
	def _conditional_insert_goods(self, tx, item):
		tx.execute("select id from s_goodsinfo where goodsurl = %s", (item['goodsurl'] ))
		result = tx.fetchone()
		if not result:
			print "begin insert s_goodsinfo"
			tx.execute(\
				"insert into s_goodsinfo (goodsurl,shopurl,goodsimgs,goodsname,goodsprice,taobaoprice,taobaourl,uptime,props,details,goodsid)\
				values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
				(item['goodsurl'],
				 item['shopurl'],
				 item['goodsimgs'],
				 item['goodsname'].encode('utf-8'),
				 item['goodsprice'],
				 item['taobaoprice'],
				 item['taobaourl'],
				 item['uptime'],
				 item['props'],
				 item['details'],
				 item['goodsid']
				 )
				)
			id = tx.connection.insert_id()
			#保存【店铺】与【商品】的关联关系
			tx.execute("insert into s_shop_goods (goodsid,shopid) values (%s,%s)", (item['goodsid'],item['shopid']))


			print "insert s_goodsinfo[%s]: done." %  item['goodsid']
			#tx.execute("insert into s_spiderlog (optype,keyid,objname) values ( %s,%s,%s)",("add",item['goodsid'],"s_goodsinfo"))
			#tx.commit()

