# -*- coding: utf-8 -*-
import time
from twisted.internet import reactor
from twisted.enterprise import adbapi
from twisted.python import log
import MySQLdb
import MySQLdb.cursors
import json
import sys




class Linkshopgoods():
	def __init__(self):
		try:
			conn=MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth',port=3306)
			cur=conn.cursor()
			cur.execute('select count(*) from s_shopinfo')
			cur.close()
			conn.close()
			
		
			log.startLogging(sys.stdout)
			self.dbpool = adbapi.ConnectionPool('MySQLdb',
				host = '127.0.0.1',
				db = 'wd_cloth',
				user = 'root',
				passwd = '',
				cursorclass = MySQLdb.cursors.DictCursor,
				charset = 'utf8',
				use_unicode = True
			)
		except MySQLdb.Error,e:
				print "Mysql Error %d: %s" % (e.args[0], e.args[1])
				
	def addlink(self):
		#建立和数据库系统的连接
		conn=MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth',port=3306)
		#获取操作游标
		cursor = conn.cursor()
		#执行SQL,创建一个数据库.
		cursor.execute("select id,shopurl from s_shopinfo where id not in ( select distinct shopid from s_shop_goods) ")
		results = cursor.fetchall()
		print "load shopinfo done."
		for r in results:
			cursor.execute("select id from s_goodsinfo where shopurl = %s ", r[1])
			goods = cursor.fetchall()
			if len(goods)>0 :
				print "shop[%s] load goodsinfo done: %s " % (r[1],len(goods))
				for g in goods:
					cursor.execute("insert into s_shop_goods ( shopid,goodsid) values (%s,%s)", (r[0],g[0]))
					print "ID of last record is %s" % int(cursor.lastrowid) #最后插入行的主键ID  
				conn.commit()
		#关闭连接，释放资源
		cursor.close();
		
	def LinkShopGoodsId(self):
		q = self.dbpool.runQuery("select id,shopurl from s_shopinfo where id not in ( select distinct shopid from s_shop_goods ) and shopurl in (select distinct shopurl from s_goodsinfo) ")
		q.addCallback(self._get_shoplist)		
		
			
		#reactor.callLater(2, reactor.stop)
		reactor.run()
		return
		
	def _get_shoplist(self,shoplist):
		print "load shopinfo done: %s " % len(shoplist)
		for shop in shoplist:
			print "beging add shop[%s] goods" % shop
			self.dbpool.runInteraction(self._conditional_insert, shop)
		
			
	def _conditional_insert(self, tx, shop):
		tx.execute("select id from s_goodsinfo where shopurl = %s", (shop['shopurl'],))
		goods = tx.fetchall()
		for g in goods:
			tx.execute("insert into s_shop_goods ( shopid,goodsid) values (%s,%s)", (shop['id'],g['id']))
			print "%s: shopid[%s] =>  goodsid[%s] " % (tx.connection.insert_id(),shop['id'],g['id']) #最后插入行的主键ID  


a = Linkshopgoods()
a.LinkShopGoodsId()
#a.addlink()
