// ==UserScript==
// @name         宝安教育
// @namespace    http://tampermonkey.net/
// @version      0.2
// @description  try to take over the world!
// @author       You
// @match        https://jxzl.baoan.edu.cn/student/home/schoolHome
// @icon         https://www.google.com/s2/favicons?sz=64&domain=github.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    const apiUrl = "https://jxzl.baoan.edu.cn/api/edu/ReportExam/GetStudentReportCard";

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
                        const scoreTitle = {
                            "Field": "Score",
                            "Title": "得分",
                            "IsPublic": false,
                            "OtherTypeId": null,
                            "SubjectIds": null,
                            "Rowspan": 1,
                            "Colspan": 1,
                            "Sort": 2,
                            "ChildrenTableHeaderList": null,
                            "IsRange": true,
                            "ParentField": null
                        }
                        const classRankingTitle = {
                            "Field": "ClassRanking",
                            "Title": "班级排名",
                            "IsPublic": false,
                            "OtherTypeId": null,
                            "SubjectIds": null,
                            "Rowspan": 1,
                            "Colspan": 1,
                            "Sort": 2,
                            "ChildrenTableHeaderList": null,
                            "IsRange": true,
                            "ParentField": null
                        }
                        const schoolRankingTitle = {
                            "Field": "SchoolRanking",
                            "Title": "学校排名",
                            "IsPublic": false,
                            "OtherTypeId": null,
                            "SubjectIds": null,
                            "Rowspan": 1,
                            "Colspan": 1,
                            "Sort": 2,
                            "ChildrenTableHeaderList": null,
                            "IsRange": true,
                            "ParentField": null
                        }
                        const rankingTitle = {
                            "Field": "Ranking",
                            "Title": "宝安排名",
                            "IsPublic": false,
                            "OtherTypeId": null,
                            "SubjectIds": null,
                            "Rowspan": 1,
                            "Colspan": 1,
                            "Sort": 2,
                            "ChildrenTableHeaderList": null,
                            "IsRange": true,
                            "ParentField": null
                        }
                        res.Data.MySubjectScore.Headers.push(scoreTitle);
                        res.Data.MySubjectScore.Headers.push(classRankingTitle);
                        res.Data.MySubjectScore.Headers.push(schoolRankingTitle);
                        res.Data.MySubjectScore.Headers.push(rankingTitle);
//                         res.Data.MySubjectScore.Rows.forEach(function(subject) {
//                             subject["Level "] = subject["Level "]+"/"+subject.Score+"/"+subject.ClassRanking+"/"+subject.SchoolRanking+"/"+subject.Ranking
//                             console.log(subject);
//                         });
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
