'use strict';

var _antd = antd;
var Spin = _antd.Spin;
var message = _antd.message;
var Popconfirm = _antd.Popconfirm;


var Num_selector = React.createClass({
    displayName: 'Num_selector',
    getInitialState: function getInitialState() {
        return { quantity: this.props.quantity };
    },

    handleChange: function handleChange(event) {
        var q = event.target.value;
        if (!isNaN(q)) this.change(q);
    },
    minus: function minus() {
        var q = this.state.quantity;
        if (q > 1) {
            q--;
        } else {
            q = 1;
        }
        this.change(q);
    },
    plus: function plus() {
        var q = this.state.quantity;
        q++;
        this.change(q);
    },
    blur: function blur() {
        var q = this.state.quantity;
        if (q < 1) q = 1;
        q = parseInt(q);
        this.change(q);
    },
    change: function change(q) {
        if (typeof this.props.max != 'undefined') {
            if (!isNaN(max) && max != '' && q >= this.props.max) {
                q = this.props.max;
            }
        }
        this.setState({ quantity: q });
        this.props.changeQuantity(q);
    },
    render: function render() {
        return React.createElement(
            'div',
            { className: 'num-selector' },
            React.createElement(
                'div',
                { className: 'op m', onClick: this.minus },
                '-'
            ),
            React.createElement('input', { type: 'text', value: this.state.quantity, onChange: this.handleChange, onBlur: this.blur }),
            React.createElement(
                'div',
                { className: 'op p', onClick: this.plus },
                '+'
            )
        );
    }
});

var Spcart_list = React.createClass({
    displayName: 'Spcart_list',
    getInitialState: function getInitialState() {
        return { price_all: this.props.spcart.price * 100 * this.props.spcart.count / 100 };
    },
    calc_price_all: function calc_price_all(quantity) {
        var price_all = this.props.spcart.price * 100 * quantity / 100;
        this.setState({ price_all: price_all });
        this.props.update_counts(this.props.spcart.id, quantity);
    },
    delete: function _delete() {
        this.props.delete_list(this.props.spcart.id);
    },
    check_Onchange: function check_Onchange(event) {
        this.props.check_item(this.props.spcart.id, event.target.checked);
    },
    render: function render() {
        var sp_url = getBaseUrl() + '/mengwu/spdetail/index/id/' + this.props.spcart.p_id;
        return React.createElement(
            'div',
            { className: 'list' },
            React.createElement(
                'div',
                { className: 'list-wrap' },
                React.createElement(
                    'div',
                    { className: 'column info' },
                    React.createElement(
                        'div',
                        { className: 'column chose' },
                        React.createElement(
                            'label',
                            null,
                            React.createElement('input', { type: 'checkbox', checked: this.props.spcart.checked ? "checked" : "", onChange: this.check_Onchange })
                        )
                    ),
                    React.createElement(
                        'div',
                        { className: 'sp-img' },
                        React.createElement(
                            'a',
                            { href: sp_url, target: '_blank' },
                            React.createElement('img', { src: this.props.spcart.icon_url })
                        )
                    ),
                    React.createElement(
                        'div',
                        { className: 'introduce' },
                        React.createElement(
                            'div',
                            { className: 'caption' },
                            React.createElement(
                                'a',
                                { href: sp_url, target: '_blank' },
                                this.props.spcart.name
                            )
                        ),
                        React.createElement(
                            'div',
                            { className: 'sorted' },
                            React.createElement(
                                'span',
                                null,
                                this.props.spcart.sku_desc
                            )
                        )
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'column price text-center' },
                    this.props.spcart.price
                ),
                React.createElement(
                    'div',
                    { className: 'column quantity text-center' },
                    React.createElement(Num_selector, { quantity: this.props.spcart.count, changeQuantity: this.calc_price_all })
                ),
                React.createElement(
                    'div',
                    { className: 'column price-all text-center' },
                    this.state.price_all
                ),
                React.createElement(
                    'div',
                    { className: 'column oper text-center' },
                    React.createElement(
                        Popconfirm,
                        { title: '确定要从购物车中删除这件商品吗？', onConfirm: this.delete },
                        React.createElement(
                            'a',
                            { href: 'javascript:void(0);' },
                            '删除'
                        )
                    )
                )
            )
        );
    }
});

var Spcart_head = React.createClass({
    displayName: 'Spcart_head',
    check_Onchange: function check_Onchange(event) {
        this.props.check_all_item(event.target.checked);
    },
    render: function render() {
        return React.createElement(
            'div',
            { className: 'head' },
            React.createElement(
                'div',
                { className: 'column info' },
                React.createElement(
                    'div',
                    { className: 'column chose-all' },
                    React.createElement(
                        'label',
                        null,
                        React.createElement('input', { type: 'checkbox', checked: this.props.checked_all ? "checked" : "", onChange: this.check_Onchange }),
                        '全选'
                    )
                ),
                '商品信息'
            ),
            React.createElement(
                'div',
                { className: 'column price text-center' },
                '单价（元）'
            ),
            React.createElement(
                'div',
                { className: 'column quantity text-center' },
                '数量'
            ),
            React.createElement(
                'div',
                { className: 'column price-all text-center' },
                '小计（元）'
            ),
            React.createElement(
                'div',
                { className: 'column oper text-center' },
                '操作'
            )
        );
    }
});

var Spcart_sure = React.createClass({
    displayName: 'Spcart_sure',
    check_Onchange: function check_Onchange(event) {
        this.props.check_all_item(event.target.checked);
    },
    delete: function _delete() {
        var spcart = this.props.spcart;
        var id = [];
        spcart.map(function (val) {
            if (val.checked === true) {
                id.push(val.id);
            }
        });
        if (id.length != 0) {
            this.props.delete_checked(id);
        } else {
            message.error('没有选中任何商品！');
        }
    },
    confirm: function confirm() {
        var spcart = this.props.spcart;
        var ids = [];
        var counts = [];
        spcart.forEach(function (val) {
            if (val.checked) {
                ids.push(val.id);
                counts.push(parseInt(val.count));
            }
        });
        if (ids.length == 0) {
            message.error('请先选择需要结算的商品！');
            return false;
        } else {
            this.props.loading(true);
            message.loading('加载中...', 0);
            //修改购物车数量
            var url = getBaseUrl() + '/mengwu/mengwuApi/spcart_edit';
            var _data = {
                id: ids.join(','),
                count: counts.join(',')
            };

            _post(url, jQuery.param(_data), { data_back: this.edit_response });
        }
    },
    edit_response: function edit_response(data) {
        var _this = this;

        if (data.status) {
            (function () {

                var ids = [];
                _this.props.spcart.forEach(function (val) {
                    if (val.checked) {
                        ids.push(val.id);
                    }
                });

                _this.props.loading(false);

                var form = $('<form></form>');
                var action = getBaseUrl() + '/mengwu/order/confirm';
                form.attr({ action: action, method: 'post', target: '_self' });
                var ids_input = $('<input type="text" name="cart_ids" />');
                var from_input = $('<input type="text" name="from" value="spcart" />');
                ids_input.attr('value', ids);
                form.append(ids_input, from_input);
                $('body').append(form);
                form.submit().remove();
            })();
        } else {
            if (typeof data.info.redirect != "undefined") {
                window.location.href = data.info.redirect;
            } else {
                message.error(data.info);
                this.props.loading(false);
            }
        }
    },
    render: function render() {
        var total_num = 0;
        var total_price = 0;
        this.props.spcart.forEach(function (n) {
            if (n.checked) {
                total_num += parseInt(n.count);
                total_price += parseInt(n.count) * 100 * n.price;
            }
        });
        total_price = total_price / 100;

        return React.createElement(
            'div',
            { className: 'sure' },
            React.createElement(
                'div',
                { className: 'column chose-all' },
                React.createElement(
                    'label',
                    null,
                    React.createElement('input', { type: 'checkbox', checked: this.props.checked_all ? "checked" : "", onChange: this.check_Onchange }),
                    '全选'
                )
            ),
            React.createElement(
                'div',
                { className: 'column delete' },
                React.createElement(
                    Popconfirm,
                    { title: '确定要从购物车中删除选中的商品吗？', onConfirm: this.delete },
                    React.createElement(
                        'a',
                        { href: 'javascript:void(0);' },
                        '删除选中'
                    )
                )
            ),
            React.createElement(
                'div',
                { className: 'column total fr' },
                '共有',
                total_num,
                '件商品，已优惠',
                React.createElement(
                    'span',
                    { className: 'dis' },
                    '0.00'
                ),
                '元，总计（不含运费）：',
                React.createElement(
                    'span',
                    { className: 'total-price' },
                    React.createElement(
                        'em',
                        { className: 'rmb' },
                        '￥'
                    ),
                    total_price
                ),
                React.createElement(
                    'a',
                    { className: 'btn confirm', href: 'javascript:void(0);', onClick: this.confirm },
                    '确认订单'
                )
            )
        );
    }
});

var Spcart = React.createClass({
    displayName: 'Spcart',
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    getInitialState: function getInitialState() {
        return { spcart: this.props.data, loading: true, todel: [] };
    },
    componentDidMount: function componentDidMount() {
        var url = getBaseUrl() + '/mengwu/mengwuApi/spcart_query';
        var data = {};
        _post(url, jQuery.param(data), { data_back: this.init_response });
    },
    init_response: function init_response(data) {
        if (data.status) {
            this.setState({ spcart: data.info.carts, loading: false });
        } else {
            if (typeof data.info.redirect != "undefined") {
                window.location.href = data.info.redirect;
            }
        }
    },
    update_counts: function update_counts(id, q) {
        var spcart = this.state.spcart.map(function (val) {
            if (val.id == id) val.count = q;
            return val;
        });
        this.setState({ spcart: spcart });
    },
    delete_list: function delete_list(id) {
        this.setState({ loading: true, todel: id });

        var ids = "";

        if (Array.isArray(id)) {
            ids = id.join(",");
        } else {
            ids = id;
        }

        var url = getBaseUrl() + '/mengwu/mengwuApi/spcart_delete';
        var data = {
            id: ids
        };

        _post(url, jQuery.param(data), { data_back: this.delete_response });
    },
    delete_response: function delete_response(data) {
        var _this2 = this;

        if (data.status) {
            (function () {
                //如果成功，删除记录
                var spcart = _this2.state.spcart;
                var todel = _this2.state.todel;
                var new_spcart = [];
                spcart.forEach(function (val) {
                    var s = true;
                    if (Array.isArray(todel)) {
                        for (var i = 0; i < todel.length; i++) {
                            if (val.id == todel[i]) s = false;
                        }
                    } else {
                        if (val.id == todel) s = false;
                    }
                    if (s) new_spcart.push(val);
                });
                message.success('删除成功');
                _this2.setState({ spcart: new_spcart, loading: false });
            })();
        } else {
            if (typeof data.info.redirect != "undefined") {
                message.error(data.info.info);
                setTimeout(function () {
                    window.location.href = data.info.redirect;
                }, 1000);
            } else {
                message.error('删除失败');
            }
        }
        this.setState({ todel: [] });
    },
    check_item: function check_item(id, checked) {
        var spcart = this.state.spcart.map(function (val) {
            if (val.id == id) val.checked = checked;
            return val;
        });
        this.setState({ spcart: spcart });
    },
    check_all_item: function check_all_item(checked) {
        var spcart = this.state.spcart.map(function (val) {
            val.checked = checked;
            return val;
        });
        this.setState({ spcart: spcart });
    },
    loading: function loading(s) {
        this.setState({ loading: s });
    },
    render: function render() {
        var checked_all = true;
        var spcart_lists = this.state.spcart.map(function (val) {
            if (!val.checked) checked_all = false;
            return React.createElement(Spcart_list, { spcart: val, update_counts: this.update_counts, delete_list: this.delete_list, check_item: this.check_item });
        }.bind(this));
        if (spcart_lists.length == 0) checked_all = false;

        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            React.createElement(
                'div',
                { className: 'spcart-wrap' },
                React.createElement(Spcart_head, { checked_all: checked_all, check_all_item: this.check_all_item }),
                spcart_lists,
                React.createElement(Spcart_sure, { loading: this.loading, spcart: this.state.spcart, checked_all: checked_all, check_all_item: this.check_all_item, delete_checked: this.delete_list })
            )
        );
    }
});

var data = {
    list: [{ id: 1, uid: 1, p_id: 1, sku_desc: "颜色：大红 尺寸：90", icon_url: "http://ww2.sinaimg.cn/crop.0.0.1080.1080.1024/005CfA2Tjw8ep0z730gxcj30u00u040l.jpg",
        price: 259, count: 2, name: "SIVENFY高支欧美田园床单纯棉四件套1.5米-1.8米宽床通用 清香怡人XXXX" }, { id: 2, uid: 1, p_id: 1, sku_desc: "颜色：大绿 尺寸：190", icon_url: "http://ww2.sinaimg.cn/crop.0.0.1242.1242.1024/d2d7a9d8jw8etui148nmtj20yi0yiwgz.jpg",
        price: 158, count: 1, name: "SIVENFY高支欧美田园床单纯棉四件套2.5米-3.8米宽床通用 清香怡人XXXX" }]
};

ReactDOM.render(
//<Spcart data={data.list} />
React.createElement(Spcart, null), document.getElementById('spcart'));

//# sourceMappingURL=common.js.map