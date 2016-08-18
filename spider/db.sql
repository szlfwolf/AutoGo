

CREATE DATABASE IF NOT EXISTS autogo DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

use autogo;

create table ag_s_city (
id int(11) PRIMARY KEY AUTO_INCREMENT,
cityname varchar(20),
cityurl varchar(120),
createtime timestamp not null default now()
);

create table ag_s_citymarket (
id int(11) PRIMARY KEY AUTO_INCREMENT,
cityname varchar(20),
marketname varchar(30),
marketurl varchar(120),
createtime timestamp not null default now()
);


create table ag_s_shopinfo ( 
id int(11) PRIMARY KEY AUTO_INCREMENT,
shopid int(11),
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


create table ag_s_goodsinfo ( 
id int(11) PRIMARY KEY AUTO_INCREMENT,
goodsid int(11),
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

create table ag_s_spiderlog (
id int(11) PRIMARY KEY AUTO_INCREMENT,
optype varchar(10) not null,
keyid int(5) not null,
objname varchar(20) not null,
createtime timestamp not null default now()
);

create table ag_s_shop_goods (
id int(11) primary key auto_increment,
shopid int(11) not null,
goodsid int(11) not null
);

create table ag_s_market_shop (
id int(11) primary key auto_increment,
marketid int(11) not null,
shopid int(11) not null
);

