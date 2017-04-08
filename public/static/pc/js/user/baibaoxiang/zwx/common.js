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

var _antd = antd;
var Pagination = _antd.Pagination;
var Spin = _antd.Spin;
var Popconfirm = _antd.Popconfirm;
var message = _antd.message;


var Zen_item = React.createClass({
    displayName: "Zen_item",
    render: function render() {
        var data = this.props.data;
        var p = data.loc_province;
        var c = data.loc_city;
        var a = mengwu.address_json;
        var b = _.find(a, ['value', p]);
        var address = "";
        if (b) {
            var pt = b.label;
            var _a = _.find(b.children, ['value', c]);
            if (_a) {
                var ct = _a.label;
                address = pt + ct;
            }
        }
        var time = this.props.type == 'send' ? data.createtime : data.win_time;
        time = new Date(time * 1000);
        var date = time.getFullYear() + '年' + time.getMonth() + '月' + time.getDate() + '日 ' + time.getHours() + ':' + time.getMinutes();
        var url = document.getElementsByTagName('base')[0].href;
        var img_list = data.show_imgs ? data.show_imgs.map(function (val) {
            return React.createElement(
                "li",
                null,
                React.createElement(
                    "a",
                    { href: "javascript:void(0);" },
                    " ",
                    React.createElement("img", { src: val })
                )
            );
        }) : null;
        return React.createElement(
            "div",
            { className: "zengwuxian" },
            React.createElement(
                "div",
                { className: "user-info" },
                React.createElement(
                    "div",
                    { className: "head" },
                    React.createElement("img", { src: data.head })
                ),
                React.createElement(
                    "div",
                    { className: "user-right" },
                    React.createElement(
                        "div",
                        { className: "name" },
                        data.nickname
                    ),
                    React.createElement(
                        "div",
                        { "class": "tp" },
                        React.createElement(
                            "span",
                            { className: "time" },
                            date
                        ),
                        React.createElement(
                            "span",
                            { className: "pos" },
                            address
                        )
                    )
                )
            ),
            React.createElement(
                "div",
                { className: "zcontent" },
                React.createElement(
                    "ul",
                    null,
                    img_list
                )
            ),
            React.createElement(
                "div",
                { className: "caption" },
                React.createElement("img", { src: url + 'Public/Mengwu/img/user/favorite/zengwuxian.png' }),
                " ",
                React.createElement(
                    "h4",
                    null,
                    React.createElement(
                        "a",
                        { href: "javascript:void(0);" },
                        data.name
                    )
                )
            )
        );
    }
});

var Zenwuxian = React.createClass({
    displayName: "Zenwuxian",
    getInitialState: function getInitialState() {
        return {
            nowPage: 1,
            num: 0,
            page_size: 5,
            list: [],
            loading: true
        };
    },
    componentDidMount: function componentDidMount() {
        this.jumpPage(1);
    },
    jumpPage: function jumpPage(page_no) {
        var url = getBaseUrl() + '/mengwu/mengwuApi/product_listGift';
        var data = {
            page_no: page_no,
            page_size: this.state.page_size,
            type: this.props.type
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ list: data.info.list, num: data.info.count, loading: false, nowPage: page_no });
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    }
                }
            }.bind(this) });
    },
    PaginationChange: function PaginationChange(page) {
        this.setState({ loading: true, list: [] });
        $('html, body').animate({
            scrollTop: 0
        }, 200);
        this.jumpPage(page);
    },
    render: function render() {
        var list = this.state.list.map(function (val) {
            return React.createElement(Zen_item, _extends({ data: val }, this.props));
        }.bind(this));

        var tip = this.props.type == 'send' ? '没有赠出任何物品哟~' : '没有收到任何物品哟~';

        var page = [];
        if (!this.state.loading) {
            if (this.state.list.length != 0) {
                page = React.createElement(
                    "div",
                    { className: "pagination-wrap" },
                    React.createElement(
                        "div",
                        { className: "pagination-middle" },
                        React.createElement(Pagination, { current: this.state.nowPage, total: this.state.num, pageSize: this.state.page_size, onChange: this.PaginationChange })
                    )
                );
            } else {
                list = React.createElement(
                    "div",
                    { className: "text-center", style: { marginTop: 50 } },
                    tip
                );
            }
        }
        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            React.createElement(
                "div",
                { className: "post-list" },
                list,
                page
            )
        );
    }
});

var Baibaoxiang_zwx = React.createClass({
    displayName: "Baibaoxiang_zwx",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    render: function render() {
        return React.createElement(
            Tabs,
            null,
            React.createElement(
                Tabpane,
                { tab: "我赠出的" },
                React.createElement(Zenwuxian, { type: "send" })
            ),
            React.createElement(
                Tabpane,
                { tab: "我收到的" },
                React.createElement(Zenwuxian, { type: "receive" })
            )
        );
    }
});

ReactDOM.render(React.createElement(Baibaoxiang_zwx, null), document.getElementById('zwx'));

//# sourceMappingURL=common.js.map