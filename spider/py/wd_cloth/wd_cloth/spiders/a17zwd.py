# -*- coding: utf-8 -*-
import scrapy


class A17zwdSpider(scrapy.Spider):
    name = "17zwd"
    allowed_domains = ["17zwd.com"]
    start_urls = (
        'http://www.17zwd.com/',
    )

    def parse(self, response):
        pass
