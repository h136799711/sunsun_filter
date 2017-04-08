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


var Comment_item = React.createClass({
    displayName: "Comment_item",
    getDate: function getDate(time) {
        var _time = new Date(time * 1000);
        var day = _time.getDate();
        day = day < 10 ? '0' + day : day;
        var month = _time.getMonth() + 1;
        month = month < 10 ? '0' + month : month;
        var m = _time.getMinutes();
        m = m < 10 ? '0' + m : m;
        var h = _time.getHours();
        h = m < 10 ? 'h' + h : m;
        _time = _time.getFullYear() + '-' + month + '-' + day + ' ' + h + ':' + m;
        return _time;
    },
    render: function render() {
        var data = this.props.data;
        var type = data.fid == 7 ? 'share' : 'question';
        var url = getBaseUrl() + '/mengwu/yuerjing/detail/type/' + type + '/tid/' + data.tid;
        if (this.props.type == 'receive') {
            return React.createElement(
                "div",
                { className: "list-item" },
                React.createElement(
                    "div",
                    { className: "item-top" },
                    React.createElement(
                        "div",
                        { className: "head" },
                        React.createElement("img", { src: data.author_info.head })
                    ),
                    React.createElement(
                        "div",
                        { className: "top-content" },
                        React.createElement(
                            "div",
                            { className: "name" },
                            data.author_info.nickname
                        ),
                        React.createElement(
                            "div",
                            { className: "text" },
                            data.comment
                        )
                    ),
                    React.createElement(
                        "a",
                        { className: "delete hide", href: "javascript:void(0);" },
                        "删除"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "rf" },
                    React.createElement(
                        "em",
                        null,
                        data.to_uid == '0' ? '评论我的育儿经：' : '评论我的回复：'
                    ),
                    React.createElement(
                        "span",
                        null,
                        data.re_comment
                    )
                ),
                React.createElement(
                    "div",
                    { className: "time" },
                    this.getDate(data.dateline),
                    React.createElement(
                        "span",
                        null,
                        "来自育儿经"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "reply-wrap" },
                    React.createElement(
                        "div",
                        { className: "btn-reply" },
                        React.createElement(
                            "a",
                            { href: url, target: "_blank" },
                            "回复"
                        )
                    )
                )
            );
        } else {
            return React.createElement(
                "div",
                { className: "list-item" },
                React.createElement(
                    "div",
                    { className: "item-top" },
                    React.createElement(
                        "div",
                        { className: "head" },
                        React.createElement("img", { src: data.author_info.head })
                    ),
                    React.createElement(
                        "div",
                        { className: "top-content" },
                        React.createElement(
                            "div",
                            { className: "name" },
                            data.author_info.nickname
                        ),
                        React.createElement(
                            "div",
                            { className: "text" },
                            data.comment
                        )
                    ),
                    React.createElement(
                        "a",
                        { className: "delete hide", href: "javascript:void(0);" },
                        "删除"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "rf" },
                    React.createElement(
                        "em",
                        null,
                        data.to_uid == '0' ? '评论育儿经：' : '评论回复：'
                    ),
                    React.createElement(
                        "span",
                        null,
                        data.re_comment
                    )
                ),
                React.createElement(
                    "div",
                    { className: "time" },
                    this.getDate(data.dateline),
                    React.createElement(
                        "span",
                        null,
                        "来自育儿经"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "reply-wrap" },
                    React.createElement(
                        "div",
                        { className: "btn-reply" },
                        React.createElement(
                            "a",
                            { href: url, target: "_blank" },
                            "查看"
                        )
                    )
                )
            );
        }
    }
});

var Comment_list = React.createClass({
    displayName: "Comment_list",
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
        var url = getBaseUrl() + '/mengwu/user/message/t/comment';
        var data = {
            page_no: page_no,
            page_size: this.state.page_size
        };
        if (this.props.type == 'receive') {
            data.type = 'receive';
        } else {
            data.type = 'send';
        }

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
            return React.createElement(Comment_item, _extends({ data: val }, this.props));
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
                    "没有收到任何回复哦~"
                );
            }
        }
        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            React.createElement(
                "div",
                { className: "cm-list" },
                list,
                page
            )
        );
    }
});

var Comment = React.createClass({
    displayName: "Comment",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    render: function render() {
        return React.createElement(
            Tabs,
            null,
            React.createElement(
                Tabpane,
                { tab: "收到的评论" },
                React.createElement(Comment_list, { type: "receive" })
            ),
            React.createElement(
                Tabpane,
                { tab: "发出的评论" },
                React.createElement(Comment_list, { type: "send" })
            )
        );
    }
});
var c_comment = document.getElementById('comment');
if (c_comment !== null) {
    ReactDOM.render(React.createElement(Comment, null), c_comment);
}

var Zan_item = React.createClass({
    displayName: "Zan_item",
    render: function render() {
        var data = this.props.data;
        var like_time = new Date(data.like_time * 1000);
        var day = like_time.getDate();
        day = day < 10 ? '0' + day : day;
        var month = like_time.getMonth() + 1;
        month = month < 10 ? '0' + month : month;
        var m = like_time.getMinutes();
        m = m < 10 ? '0' + m : m;
        var h = like_time.getHours();
        h = m < 10 ? 'h' + h : m;
        like_time = like_time.getFullYear() + '-' + month + '-' + day + ' ' + h + ':' + m;
        return React.createElement(
            "div",
            { className: "list-item" },
            React.createElement(
                "div",
                { className: "item-top" },
                React.createElement(
                    "div",
                    { className: "head" },
                    React.createElement("img", { src: data.head })
                ),
                React.createElement(
                    "div",
                    { className: "top-content" },
                    React.createElement(
                        "div",
                        { className: "name" },
                        data.nickname
                    ),
                    React.createElement(
                        "div",
                        { className: "text" },
                        "赞了我的育儿经"
                    )
                ),
                React.createElement(
                    "a",
                    { className: "delete hide", href: "javascript:void(0);" },
                    "删除"
                )
            ),
            React.createElement(
                "div",
                { className: "rf" },
                React.createElement(
                    "em",
                    { className: "pink" },
                    "@我："
                ),
                React.createElement(
                    "span",
                    null,
                    data.message
                )
            ),
            React.createElement(
                "div",
                { className: "time" },
                like_time,
                React.createElement(
                    "span",
                    null,
                    "来自育儿经"
                )
            )
        );
    }
});

var Zan_list = React.createClass({
    displayName: "Zan_list",
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
        var url = getBaseUrl() + '/mengwu/user/message/t/zan';
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
            return React.createElement(Zan_item, { data: val });
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
                    "没有收到任何点赞哦~"
                );
            }
        }
        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            React.createElement(
                "div",
                { className: "cm-list" },
                list,
                page
            )
        );
    }
});

var Zan = React.createClass({
    displayName: "Zan",
    render: function render() {
        return React.createElement(Zan_list, null);
    }
});

var c_zan = document.getElementById('zan');
if (c_zan != null) {
    ReactDOM.render(React.createElement(Zan, null), c_zan);
}

//# sourceMappingURL=common.js.map