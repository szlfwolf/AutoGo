-- 
-- 数据库: `thinkcmfx`
-- 

-- --------------------------------------------------------
-- 
-- 表的结构 `wdzs_user_token`
-- 

CREATE TABLE `ag_wdzs_api_info` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`API_TYPE` varchar(255) NOT NULL COMMENT 'app类型，支持1688',
	`API_CODE` varchar(255) NOT NULL COMMENT 'app代码',
	`API_VALUE` varchar(255) NOT NULL COMMENT 'app代码对应值',
	`createtime` datetime NOT NULL DEFAULT  NOW() COMMENT '创建时间',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO ag_wdzs_api_info (id,api_type,api_code,api_value)VALUES (1,'1688','APP_KEY','');
INSERT INTO ag_wdzs_api_info (id,api_type,api_code,api_value)VALUES (2,'1688','APP_CODE','');
INSERT INTO ag_wdzs_api_info (id,api_type,api_code,api_value)VALUES (3,'1688','REDIRECT_URI','http://localhost/autogo/index.php/Wdzs/Index/token');


CREATE TABLE `ag_wdzs_user_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户授权信息表id',
  `aliId` varchar(255) NOT NULL COMMENT '阿里巴巴集团统一的id',
  `resource_owner` varchar(255) NOT NULL COMMENT '登录id',
  `memberId` varchar(255) NOT NULL COMMENT '会员接口id',
  `expires_in` varchar(255) NOT NULL COMMENT '访问令牌有效时长,10小时',
  `refresh_token` varchar(255) NOT NULL COMMENT '长时令牌',
  `access_token` varchar(255) NOT NULL COMMENT '访问令牌',
  `refresh_token_timeout` text COMMENT 'refreshToken的过期时间',
  `createtime` datetime NOT NULL DEFAULT  NOW() COMMENT '授权时间',
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ag_wdzs_api_category` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
	`API_TYPE` varchar(255) NOT NULL COMMENT 'api类型，1688',
	`categoryid` varchar(255) NOT NULL COMMENT 'api类型，1688',
	`level` varchar(255) NOT NULL COMMENT 'api类型，1688',
	`enName` varchar(255) NOT NULL COMMENT '类目名称',
	`isLeaf` varchar(255) NOT NULL COMMENT '是否叶子节点',
	`childIDs` varchar(255) NOT NULL COMMENT '',
	
)
