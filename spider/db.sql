

CREATE DATABASE IF NOT EXISTS wd_cloth DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

create table s_shopinfo ( 
id int(11) PRIMARY KEY AUTO_INCREMENT,
shopname varchar(80),
shopurl varchar(120),
shopimg varchar(120),
marketname varchar(80),
marketfloor varchar(20),
marketdk varchar(20),
category varchar(20),
tip varchar(20),
qqinfo varchar(120),
wwinfo varchar(120),
qqnum varchar(30),
wwname varchar(80),
phonenum varchar(30),
tburl varchar(120),
props text,
createtime timestamp not null default now()

);


create table s_goodsinfo ( 
id int(11) PRIMARY KEY AUTO_INCREMENT,
goodsname varchar(150),
goodsimgs varchar(2000),
goodsurl varchar(120),
shopurl varchar(120),
goodsprice decimal(5,2),
taobaoprice decimal(5,2),
taobaourl varchar(120),
uptime datetime,
props text,
details text,
createtime timestamp not null default now()
);

create table s_spiderlog (
id int(11) PRIMARY KEY AUTO_INCREMENT,
optype varchar(10) not null,
keyid int(5) not null,
objname varchar(20) not null,
createtime timestamp not null default now()
);
