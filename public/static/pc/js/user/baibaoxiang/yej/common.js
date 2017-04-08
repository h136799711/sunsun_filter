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


var Yu_item = React.createClass({
    displayName: "Yu_item",
    render: function render() {
        var data = this.props.data;
        var fenlei = data.fid == 7 ? '热门分享' : data.fid == 8 ? '宝妈提问' : '';
        var type = data.fid == 7 ? 'share' : data.fid == 8 ? 'question' : '';
        var url = document.getElementsByTagName('base')[0].href;
        var t_url = getBaseUrl() + '/mengwu/yuerjing/detail/type/' + type + '/tid/' + data.tid;
        return React.createElement(
            "div",
            { className: "yuerjing" },
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
                        data.author
                    ),
                    React.createElement(
                        "div",
                        { className: "reason" },
                        React.createElement(
                            "div",
                            { className: "text" },
                            React.createElement(
                                "a",
                                { href: t_url, target: "_blank" },
                                data.subject || '无标题'
                            )
                        ),
                        React.createElement(
                            "div",
                            { className: "fenlei" },
                            fenlei
                        )
                    )
                )
            ),
            React.createElement(
                "div",
                { className: "yuanwen" },
                React.createElement(
                    "a",
                    { href: t_url, target: "_blank" },
                    data.message
                )
            )
        );
    }
});

var Yuerjing = React.createClass({
    displayName: "Yuerjing",
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
        var url = getBaseUrl() + '/mengwu/mengwuApi/baibaoxiang_yuerjing/type/' + this.props.type;
        var data = {
            page_no: page_no,
            page_size: this.state.page_size
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
            return React.createElement(Yu_item, { data: val });
        }.bind(this));
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
                    "没有发表任何帖子哟~"
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

var Baibaoxiang_yej = React.createClass({
    displayName: "Baibaoxiang_yej",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    render: function render() {
        return React.createElement(
            Tabs,
            null,
            React.createElement(
                Tabpane,
                { tab: "热门分享" },
                React.createElement(Yuerjing, { type: "share" })
            ),
            React.createElement(
                Tabpane,
                { tab: "宝妈提问" },
                React.createElement(Yuerjing, { type: "question" })
            )
        );
    }
});

ReactDOM.render(React.createElement(Baibaoxiang_yej, null), document.getElementById('yej'));

//# sourceMappingURL=common.js.map