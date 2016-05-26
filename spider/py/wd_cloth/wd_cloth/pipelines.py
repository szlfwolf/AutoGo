# -*- coding: utf-8 -*-
import scrapy
import time
from twisted.enterprise import adbapi
from twisted.python import log
import MySQLdb
import MySQLdb.cursors
import json
import sys


class WdClothPipeline(object):

	def __init__(self):
		try:
			conn=MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth',port=3306)
			cur=conn.cursor()
			cur.execute('select count(*) from s_shopinfo')
			cur.close()
			conn.close()
			print "ok"
		except MySQLdb.Error,e:
			print "Mysql Error %d: %s" % (e.args[0], e.args[1])
		
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
		
		

	def process_item(self, item, spider):
		if item.get('shopname') :
			query = self.dbpool.runInteraction(self._conditional_insert, item)
		elif item.get('shopinfourl') :
			query = self.dbpool.runInteraction(self._conditional_update_shopinfo, item)
		elif item.get('goodsurl') :
			print "save item :%s" % item['goodsurl']
			query = self.dbpool.runInteraction(self._conditional_insert_goods, item)
		
		query.addErrback(self.handle_error)
		return item

	def handle_error(self, e):
		print 'error: %s' % e
		

	def _conditional_insert(self, tx, item):
		tx.execute("select * from s_shopinfo where shopurl = %s", (item['shopurl'] ))
		result = tx.fetchone()
		if not result:
			#print 'shop[%s] already exist!' % item['shopname']
		#else:
			tx.execute(\
				"insert into s_shopinfo (shopname,shopurl,marketname,marketfloor,marketdk,category,tip,qqinfo,wwinfo,props)\
				values (%s, %s,%s, %s,%s, %s,%s, %s,%s,%s)",
				(item['shopname'].encode('utf-8'),
				 item['shopurl'],
				 item['marketname'].encode('utf-8'),
				 item['marketfloor'].encode('utf-8'),
				 item['marketdk'].encode('utf-8'),
				 item['category'].encode('utf-8'),
				 item['tip'].encode('utf-8'),
				 item['qqinfo'].encode('utf-8'),
				 item['wwinfo'].encode('utf-8'),
				 json.dumps(item['props'],ensure_ascii=False))
				)
			shopid = tx.connection.insert_id()
			print "insert s_goodsinfo[%s]: done." % shopid
			tx.execute("insert into s_spiderlog (optype,keyid,objname) values (%s,%s,%s)",("add",shopid,"s_shopinfo"))
			

	def _conditional_update_shopinfo(self, tx, item):
		tx.execute("select id,wwname from s_shopinfo where shopurl = %s", (item['shopinfourl'] ))
		result = tx.fetchone()
		if result:
			if not result['wwname']:
				print 'shop url[%s] already exist and begin to update ...' % item['shopinfourl']
				tx.execute(\
					"update s_shopinfo set qqnum=%s,wwname=%s,phonenum=%s,tburl=%s, updatetime=now() where shopurl = %s",
					(item['qqnum'],item['wwname'].encode('utf-8'),item['phonenum'],item['tburl'],item['shopinfourl'])
					)
			shopid = result['id']
			print "insert s_goodsinfo[%s]: done." % shopid
			tx.execute("insert into s_spiderlog (optype,keyid,objname) values (%s,%s,%s)",("update",shopid,"s_shopinfo"))



	def _conditional_insert_goods(self, tx, item):
		tx.execute("select * from s_goodsinfo where goodsurl = %s", (item['goodsurl'] ))
		result = tx.fetchone()
		if not result:
			print "begin insert s_goodsinfo"
			tx.execute(\
				"insert into s_goodsinfo (goodsurl,shopurl,goodsimgs,goodsname,goodsprice,taobaoprice,taobaourl,uptime,props,details)\
				values (%s,%s,%s,%s,%s,%s,%s,%s,%s.%s)",
				(item['goodsurl'],
				 item['shopurl'],
				 item['goodsimgs'],
				 item['goodsname'].encode('utf-8'),
				 item['goodsprice'],
				 item['taobaoprice'],
				 item['taobaourl'],
				 item['uptime'],
				 item['props'],
				 item['details']
				 )
				)
			goodsid = tx.connection.insert_id()
			print "insert s_goodsinfo[%s]: done." % goodsid
			tx.execute("insert into s_spiderlog (optype,keyid,objname) values ( %s,%s,%s)",("add",goodsid,"s_goodsinfo"))
			#tx.commit()

