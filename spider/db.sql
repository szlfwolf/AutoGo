

CREATE DATABASE IF NOT EXISTS wd_cloth DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

create table s_shopinfo ( 
id int(5) PRIMARY KEY AUTO_INCREMENT,
shopname varchar(80),
shopurl varchar(120),
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
props varchar(2000)


);

insert into s_shopinfo (shopname,shopurl,marketname,marketfloor,marketdk,category,tip,qqinfo,wwinfo,props)

create table s_goodsinfo ( 
id int(5) PRIMARY KEY AUTO_INCREMENT,
goodsname varchar(150),
goodsimgs varchar(2000),
goodsurl varchar(120),
goodsprice decimal(5,2),
taobaoprice decimal(5,2),
taobaourl varchar(120),
uptime datetime,
props varchar(2000),
details varchar(4000),
createtime timestamp not null default now()
);
