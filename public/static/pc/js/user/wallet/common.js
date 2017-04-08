"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var Tabpane = React.createClass({
    displayName: "Tabpane",
    render: function render() {
        return;
    }
});

var Tabnav = React.createClass({
    displayName: "Tabnav",
    onClick: function onClick() {
        this.props.tabClick(this.props.tab_id);
    },
    render: function render() {
        return React.createElement(
            "div",
            { className: this.props.class, onClick: this.onClick },
            this.props.tab
        );
    }
});

var Tabs = React.createClass({
    displayName: "Tabs",
    getInitialState: function getInitialState() {
        var live = React.Children.map(this.props.children, function (val) {
            return false;
        });
        return { now_tab: 0, live: live };
    },
    tabClick: function tabClick(n) {
        this.setState({ now_tab: n });
    },
    render: function render() {
        var tab_navs = [];
        var tab_contents = [];

        var children = this.props.children;
        var now_tab = this.state.now_tab;
        var live = this.state.live;

        React.Children.forEach(children, function (val, n) {
            var tab_class = "tab-nav";
            var list_class = "tab-tabpane tab-tabpane-hidden";
            var aria_hidden = "false";
            if (now_tab == n) {
                tab_class = "tab-nav tab-selected";
                list_class = "tab-tabpane";
                aria_hidden = "true";
            }
            if (now_tab == n) live[n] = true;
            tab_navs.push(React.createElement(Tabnav, _extends({}, val.props, { tab_id: n, tabClick: this.tabClick, "class": tab_class, now_tab: now_tab })));

            if (live[n]) tab_contents.push(React.createElement(
                "div",
                { "aria-hidden": aria_hidden, className: list_class },
                val.props.children
            ));
        }.bind(this));

        return React.createElement(
            "div",
            { className: "tab" },
            React.createElement(
                "div",
                { className: "tab-list" },
                tab_navs
            ),
            React.createElement(
                "div",
                { className: "tab-content" },
                tab_contents
            )
        );
    }
});

var Red_envelope = React.createClass({
    displayName: "Red_envelope",
    render: function render() {

        var list = this.props.relist.map(function (d) {
            var tip = "";
            switch (d.state) {
                case 0:
                    tip = "立即使用";
                    break;
                case 1:
                    tip = "已使用";
                    break;
                case 2:
                    tip = "已过期";
                    break;
            }
            return React.createElement(
                "li",
                null,
                React.createElement(
                    "div",
                    { className: "re-detail" },
                    React.createElement(
                        "div",
                        { className: "re-title" },
                        React.createElement(
                            "h4",
                            null,
                            d.name
                        ),
                        React.createElement(
                            "span",
                            null,
                            typeof d.explain != 'undefined' ? '（' + d.explain + '）' : ''
                        )
                    ),
                    React.createElement(
                        "div",
                        { className: "re-content" },
                        React.createElement(
                            "div",
                            { className: "l" },
                            d.money
                        ),
                        React.createElement(
                            "div",
                            { className: "r" },
                            React.createElement(
                                "h3",
                                null,
                                d.type
                            ),
                            React.createElement(
                                "h5",
                                null,
                                "有效期:",
                                d.deadline
                            )
                        )
                    )
                ),
                d.state == 0 ? React.createElement(
                    "a",
                    { href: "javascript:void(0);" },
                    React.createElement(
                        "div",
                        { className: "re-tip" },
                        tip
                    )
                ) : React.createElement(
                    "div",
                    { className: "re-tip" },
                    tip
                )
            );
        });
        return React.createElement(
            "div",
            { className: "red-envelope-content" },
            React.createElement(
                "ul",
                null,
                list
            )
        );
    }
});

// const Redata = [
//     {tab: "未使用" , list:[
//         {state: 0, name: "衣成天品专场红包", explain: "仅限品牌专场使用", money: 30, type: "红包", deadline: "2016-06-01至2016-06-10"},
//         {state: 0, name: "衣成天品专场红包", explain: "仅限品牌专场使用", money: 40, type: "红包", deadline: "2016-06-01至2016-06-10"}
//     ]},
//     {tab: "已使用" , list:[
//         {state: 1, name: "世纪天成专场红包", explain: "仅限品牌专场使用", money: 50, type: "红包", deadline: "2016-04-08至2016-04-10"}
//     ]},
//     {tab: "已过期" , list:[
//         {state: 2, name: "西山居专场红包", explain: "仅限品牌专场使用", money: 100, type: "红包", deadline: "2016-04-08至2016-04-10"}
//     ]}
// ];

var Re_box = React.createClass({
    displayName: "Re_box",
    getInitialState: function getInitialState() {
        return {
            redata: this.dealData(data_info)
        };
    },
    dealData: function dealData(data) {
        var redata = [{ tab: "未使用", list: [] }, { tab: "已使用", list: [] }, { tab: "已过期", list: [] }];
        console.log(data);
        data.forEach(function (val) {
            var d = { state: 0, name: val.name, money: val.money, type: "红包", deadline: "" };
            var start = parseInt(val.get_time) * 1000;
            var end = parseInt(val.expire) * 1000;
            d.deadline = this.time2Date(start) + '至' + this.time2Date(end);
            //判断未使用
            if (parseInt(val.use_status)) {
                //已使用
                redata[1].list.push(d);
                d.state = 1;
            } else {
                //未使用
                d.state = 0;

                // 判断是否过期
                if (Date.parse(new Date()) > end) {
                    //已过期
                    d.state = 2;
                    redata[2].list.push(d);
                } else {
                    redata[0].list.push(d);
                }
            }
        }.bind(this));
        return redata;
    },
    time2Date: function time2Date(time) {
        var date = new Date(time);
        var m = date.getMonth() + 1;
        m = m.length == 1 ? '0' + m : m;
        var d = date.getDate();
        d = d.length == 1 ? '0' + d : d;
        return date.getFullYear() + '-' + m + '-' + d;
    },
    render: function render() {
        var tabpanes = this.state.redata.map(function (val) {
            return React.createElement(
                Tabpane,
                { tab: val.tab },
                React.createElement(Red_envelope, { relist: val.list })
            );
        });
        return React.createElement(
            Tabs,
            null,
            tabpanes
        );
    }
});

var Re_tabs = document.getElementById('re_tabs');
if (Re_tabs !== null) {
    ReactDOM.render(React.createElement(Re_box, null), Re_tabs);
}

// const Ccdata = [
//     {tab: "未使用" , list:[
//         {state: 0, name: "满200元使用", money: 30, type: "优惠券", deadline: "2016-06-01至2016-06-10"},
//         {state: 0, name: "满100元使用", money: 40, type: "优惠券", deadline: "2016-06-01至2016-06-10"}
//     ]},
//     {tab: "已使用" , list:[
//         {state: 1, name: "满50元使用", money: 50, type: "优惠券", deadline: "2016-04-08至2016-04-10"}
//     ]},
//     {tab: "已过期" , list:[
//         {state: 2, name: "满300元使用", money: 100, type: "优惠券", deadline: "2016-04-08至2016-04-10"}
//     ]}
// ];

var Cc_tabs = document.getElementById('cc_tabs');
if (Cc_tabs !== null) {
    ReactDOM.render(React.createElement(Re_box, null), Cc_tabs);
}

//# sourceMappingURL=common.js.map