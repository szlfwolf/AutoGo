-- 
-- 数据库: `thinkcmfx`
-- 

-- --------------------------------------------------------
-- 
-- 表的结构 `wdzs_user_token`
-- 

CREATE TABLE `wdzs_user_token` (
  `token_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `aliId` varchar(255) NOT NULL COMMENT '阿里巴巴集团统一的id',
  `resource_owner` varchar(255) NOT NULL COMMENT '登录id',
  `memberId` varchar(255) NOT NULL COMMENT '会员接口id',
  `expires_in` varchar(255) NOT NULL COMMENT '访问令牌有效时长,10小时',
  `refresh_token` varchar(255) NOT NULL COMMENT '长时令牌',
  `access_token` varchar(255) NOT NULL COMMENT '访问令牌',
  `refresh_token_timeout` text COMMENT 'refreshToken的过期时间',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '授权时间',
  PRIMARY KEY (`token_id`),
  KEY `access_token` (`access_token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

