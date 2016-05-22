# -*- coding: utf-8 -*-
import scrapy
import time
from twisted.enterprise import adbapi
import MySQLdb
import MySQLdb.cursors
import json


class WdClothPipeline(object):

	def __init__(self):
		try:
			conn=MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth',port=3306)
			cur=conn.cursor()
			cur.execute('select * from s_shopinfo')
			cur.close()
			conn.close()
			print "ok"
		except MySQLdb.Error,e:
			print "Mysql Error %d: %s" % (e.args[0], e.args[1])
		
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
			query.addErrback(self.handle_error)
		elif item.get('shopinfourl') :
			query = self.dbpool.runInteraction(self._conditional_insert_shopinfo, item)
			query.addErrback(self.handle_error)
		
		return item

	def handle_error(self, e):
		print 'error: %s' % e

	def _conditional_insert_shopinfo(self, tx, item):
		tx.execute("select qqnum from s_shopinfo where shopurl = %s", (item['shopinfourl'] ))
		result = tx.fetchone()
		if result:
			if not result['qqnum']:
				print 'shop url[%s] already exist and qqnum is null ...' % item['shopinfourl']
				
			#else:
				tx.execute(\
					"update s_shopinfo set qqnum=%s,wwname=%s,phonenum=%s,tburl=%s where shopurl = %s",
					(item['qqnum'],item['wwname'].encode('utf-8'),item['phonenum'],item['tburl'],item['shopinfourl'])
					)

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