

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
props varchar(150)


);

insert into s_shopinfo (shopname,shopurl,marketname,marketfloor,marketdk,category,tip,qqinfo,wwinfo,props)