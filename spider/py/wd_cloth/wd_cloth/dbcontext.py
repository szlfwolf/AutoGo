from twisted.enterprise import adbapi
import MySQLdb
import MySQLdb.cursors

class dbcontext(object):
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
		self.dbpool = adbapi.ConnectionPool('MySQLdb',
			host = '127.0.0.1',
			db = 'wd_cloth',
			user = 'root',
			passwd = '',
			cursorclass = MySQLdb.cursors.DictCursor,
			charset = 'utf8',
			use_unicode = True
		)
	
	def getshops(self):
		conn = MySQLdb.connect(host='localhost',user='root',passwd='',db='wd_cloth',port=3306)
		cur = conn.cursor()
		count = cur.execute('select shopurl from s_shopinfo')
		for row in cur.fetchall():
			yield row[0]