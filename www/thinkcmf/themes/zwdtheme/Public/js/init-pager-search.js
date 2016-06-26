function ZWDPager(name, _pageSize, _totalRecord, _pagerCssName) { //null
    this._totalRecord = _totalRecord;
    this.pagerName = name;
    this.pageIndex = 1;
    if (isNaN(_pageSize)) _pageSize = 1;
    if (isNaN(_totalRecord)) _totalRecord = 1;
    var pageCountTmp = Math.ceil(_totalRecord / _pageSize);
    this.pageCount = pageCountTmp;
    this.argName = 'page';
    this.showTimes = 1;

    if (_pagerCssName == '' || typeof (_pagerCssName) == 'undefined')
        _pagerCssName = 'badoo'; //强制

    this._pagerCssName = _pagerCssName;
}

ZWDPager.prototype.getPage = function () { //url get page
    var args = location.search;
    var reg = new RegExp('[\?&]?' + this.argName + '=([^&]*)[&$]?', 'gi');
    var chk = args.match(reg);
    this.pageIndex = RegExp.$1;
}
ZWDPager.prototype.checkPages = function () { //进行当前页数和总页数的验证
    if (isNaN(parseInt(this.pageIndex))) this.pageIndex = 1;
    if (isNaN(parseInt(this.pageCount))) this.pageCount = 1;
    if (this.pageIndex < 1) this.pageIndex = 1;
    if (this.pageCount < 1) this.pageCount = 1;
    if (this.pageIndex > this.pageCount) this.pageIndex = this.pageCount;
    this.pageIndex = parseInt(this.pageIndex);
    this.pageCount = parseInt(this.pageCount);
}
ZWDPager.prototype.createHtml = function (mode, isTotalShow) { //html
    var strHtml = '', prevPage = this.pageIndex - 1, nextPage = this.pageIndex + 1;
    if (mode == '' || typeof (mode) == 'undefined')
        mode = 1;

    if (isTotalShow == '' || typeof (isTotalShow) == 'undefined')
        isTotalShow = 0;

    if (prevPage < 1) { } else {
        strHtml += '<span title="首页"><a href="javascript:' + this.pagerName + '.toPage(1);"> 首页</a></span>';
        strHtml += '<span title="上一页"><a href="javascript:' + this.pagerName + '.toPage(' + prevPage + ');">上一页</a></span>';
    }
    if (this.pageIndex % 5 == 0) {
        var startPage = this.pageIndex - 4;
    } else {
        var startPage = this.pageIndex - this.pageIndex % 5 + 1;
    }
    if (startPage > 5) {
        strHtml += '<span title="前 5 页"><a href="javascript:' + this.pagerName + '.toPage(' + (startPage - 1) + ');">..</a></span>';
    }

    for (var i = startPage; i < startPage + 5; i++) {
        if (i > this.pageCount) break;
        if (i == this.pageIndex) {
            strHtml += '<span title="第 ' + i + ' 页" class=current>' + i + '</span>';
        } else {
            strHtml += '<a href="javascript:' + this.pagerName + '.toPage(' + i + ');">' + i + '</a>';
        }
    }

    if (this.pageCount >= startPage + 5) {
        strHtml += '<span title="后 5 页"><a href="javascript:' + this.pagerName + '.toPage(' + (startPage + 5) + ');">..</a></span>';
    }

    if (nextPage > this.pageCount) { }
    else {
        strHtml += '<span title="下一页"><a href="javascript:' + this.pagerName + '.toPage(' + nextPage + ');">下一页</a></span>';
    }

    strHtml += '&nbsp;<span> 共' + this.pageCount + ' 页';
    if (isTotalShow == 1)
        strHtml += "&nbsp;(" + this._totalRecord + ")";
    strHtml += "</span>";

    strHtml = '<div class=' + this._pagerCssName + '>' + strHtml + '</div>';
    return strHtml;
}
ZWDPager.prototype.createUrl = function (page) { //跳转url
    if (isNaN(parseInt(page))) page = 1;
    if (page < 1) page = 1;
    if (page > this.pageCount) page = this.pageCount;
    var url = location.protocol + '//' + location.host + location.pathname;
    var args = location.search;
    var reg = new RegExp('([\?&]?)' + this.argName + '=[^&]*[&$]?', 'gi');
    args = args.replace(reg, '$1');
    if (args == '' || args == null) {
        args += '?' + this.argName + '=' + page;
    } else if (args.substr(args.length - 1, 1) == '?' || args.substr(args.length - 1, 1) == '&') {
        args += this.argName + '=' + page;
    } else {
        args += '&' + this.argName + '=' + page;
    }
    return url + args;
}
ZWDPager.prototype.toPage = function (page) { //跳转
    var turnTo = 1;
    if (typeof (page) == 'object') {
        turnTo = page.options[page.selectedIndex].value;
    } else {
        turnTo = page;
    }
    self.location.href = this.createUrl(turnTo);
}
ZWDPager.prototype.printHtml = function (mode, isTotalShow) { //显html
    this.getPage();
    this.checkPages();
    this.showTimes += 1;
    document.write('<div id="pages_' + this.pagerName + '_' + this.showTimes + '" class="pages"></div>');
    document.getElementById('pages_' + this.pagerName + '_' + this.showTimes).innerHTML = this.createHtml(mode, isTotalShow);

}
ZWDPager.prototype.formatInputPage = function (e) { //限输入
    var ie = navigator.appName == 'Microsoft Internet Explorer' ? true : false;
    if (!ie) var key = e.which;
    else var key = event.keyCode;
    if (key == 8 || key == 46 || (key >= 48 && key <= 57)) return true;
    return false;
}