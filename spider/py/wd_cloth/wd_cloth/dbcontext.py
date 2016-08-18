from twisted.enterprise import adbapi
import MySQLdb
import MySQLdb.cursors

class dbcontext(object):

	def getshops(self):
		conn = MySQLdb.connect(host='localhost',user='root',passwd='',db='autogo',port=3306)
		cur = conn.cursor()
		count = cur.execute('select shopurl from ag_s_shopinfo')
		for row in cur.fetchall():
			yield row[0]