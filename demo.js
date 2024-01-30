// ==UserScript==
// @name         demo
// @namespace    http://tampermonkey.net/
// @version      0.2
// @description  try to take over the world!
// @author       You
// @match        https://*/*
// @icon         https://www.google.com/s2/favicons?sz=64&domain=github.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    const apiUrl = "/notification/user/";

    // Your code here...
    const originOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function (_, url) {
        if (url.indexOf(apiUrl) >= 0) {
            const xhr = this;
            const getter = Object.getOwnPropertyDescriptor(
                XMLHttpRequest.prototype,
                "response"
            ).get;
            Object.defineProperty(xhr, "responseText", {
                get: () => {
                    let result = getter.call(xhr);
                    try {
                        const res = JSON.parse(result);
                        console.log(res)
                        res[0].userName=res[0].userName+"-"+res[0].huaweiID
                        return JSON.stringify(res);
                    } catch (e) {
                        return result;
                    }
                },
            });
        }
        originOpen.apply(this, arguments);
    };
    // end;
})();
