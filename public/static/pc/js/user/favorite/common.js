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
        var _this = this;

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
                            data.create_time_show
                        ),
                        React.createElement(
                            "span",
                            { className: "pos" },
                            address
                        )
                    ),
                    React.createElement(
                        Popconfirm,
                        { title: "确定要取消收藏吗？", onConfirm: function onConfirm() {
                                return _this.props.delete(data.pid);
                            } },
                        React.createElement(
                            "a",
                            { className: "delete", href: "javascript:void(0);" },
                            React.createElement("img", { src: url + 'Public/Mengwu/img/user/favorite/delete.png' })
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

var Yu_item = React.createClass({
    displayName: "Yu_item",
    render: function render() {
        var _this2 = this;

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
                    React.createElement("img", { src: data.author_info.head || null })
                ),
                React.createElement(
                    "div",
                    { className: "user-right" },
                    React.createElement(
                        "div",
                        { className: "name" },
                        data.author_info.nickname || 'null'
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
                    ),
                    React.createElement(
                        Popconfirm,
                        { title: "确定要取消收藏吗？", onConfirm: function onConfirm() {
                                return _this2.props.delete(data.tid);
                            } },
                        React.createElement(
                            "a",
                            { href: "javascript:void(0);", className: "delete" },
                            React.createElement("img", { src: url + 'Public/Mengwu/img/user/favorite/delete.png' })
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
        var url = getBaseUrl() + '/mengwu/mengwuApi/product_favorites_gift';
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
    delete: function _delete(pid) {
        this.setState({ loading: true });
        var url = getBaseUrl() + '/mengwu/mengwuApi/zengwuxian_favorite_favorites';
        var data = {
            pid: pid,
            favorite_value: 0
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ loading: false });
                    message.success('取消成功');
                    this.jumpPage(this.state.nowPage);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 200);
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    }
                }
            }.bind(this) });
    },
    render: function render() {
        var list = this.state.list.map(function (val) {
            return React.createElement(Zen_item, { data: val, "delete": this.delete });
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
                    "没有收藏任何帖子哟~"
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
        var url = getBaseUrl() + '/mengwu/yuerjing/yej_getFavorites';
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
    delete: function _delete(tid) {
        this.setState({ loading: true });
        var url = getBaseUrl() + '/mengwu/mengwuApi/post_favorites';
        var data = {
            tid: tid,
            favorite_value: 0
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ loading: false });
                    message.success('取消成功');
                    this.jumpPage(this.state.nowPage);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 200);
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    }
                }
            }.bind(this) });
    },
    render: function render() {
        var list = this.state.list.map(function (val) {
            return React.createElement(Yu_item, { data: val, "delete": this.delete });
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
                    "没有收藏任何帖子哟~"
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

var Favorite_posts = React.createClass({
    displayName: "Favorite_posts",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    render: function render() {
        return React.createElement(
            Tabs,
            null,
            React.createElement(
                Tabpane,
                { tab: "赠无限" },
                React.createElement(Zenwuxian, null)
            ),
            React.createElement(
                Tabpane,
                { tab: "育儿经" },
                React.createElement(Yuerjing, null)
            )
        );
    }
});

ReactDOM.render(React.createElement(Favorite_posts, null), document.getElementById('favorite_post'));

//# sourceMappingURL=common.js.map