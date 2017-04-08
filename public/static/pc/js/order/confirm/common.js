"use strict";

var _mengwu = mengwu;
var option = _mengwu.address_json;
var _antd = antd;
var Modal = _antd.Modal;
var Cascader = _antd.Cascader;
var Input = _antd.Input;
var Checkbox = _antd.Checkbox;
var message = _antd.message;
var Spin = _antd.Spin;
var Select = _antd.Select;

var Address = function Address() {
    var list = this.state.data.map(function (val) {
        var _this = this;

        var cl = val.selected == true ? "address-list selected" : "address-list";
        var data = {};
        _.assign(data, val);
        return React.createElement(
            "li",
            { className: cl, onClick: function onClick() {
                    return _this.selectAddress(data.id);
                } },
            React.createElement(
                "div",
                null,
                React.createElement(
                    "p",
                    null,
                    React.createElement(
                        "em",
                        null,
                        val.address
                    ),
                    " (",
                    val.contactname,
                    " 收) ",
                    React.createElement(
                        "span",
                        null,
                        "默认"
                    )
                ),
                React.createElement(
                    "p",
                    null,
                    val.detail
                ),
                React.createElement(
                    "p",
                    null,
                    val.phone
                ),
                React.createElement(
                    "p",
                    null,
                    React.createElement(
                        "a",
                        { href: "javascript:void(0);", onClick: function onClick() {
                                return _this.edit_address(data);
                            } },
                        "修改"
                    )
                )
            )
        );
    }.bind(this));
    return React.createElement(
        "div",
        { className: "address-wrap" },
        React.createElement(
            "ul",
            null,
            list
        ),
        React.createElement(
            "a",
            { className: "btn add-address", href: "javascript:void(0);", onClick: this.addressAdd },
            "使用新地址"
        )
    );
};

var Order_Items = function Order_Items() {
    var sp_url_pre = getBaseUrl() + '/mengwu/spdetail/index/id/';
    var list = order_items.map(function (val) {
        return React.createElement(
            "tr",
            { className: "order-item" },
            React.createElement(
                "td",
                null,
                React.createElement(
                    "div",
                    { className: "item-info" },
                    React.createElement(
                        "div",
                        { className: "sp-img" },
                        React.createElement(
                            "a",
                            { href: sp_url_pre + val.p_id, target: "_blank" },
                            React.createElement("img", { src: val.icon_url })
                        )
                    ),
                    React.createElement(
                        "div",
                        { className: "introduce" },
                        React.createElement(
                            "div",
                            { className: "caption" },
                            React.createElement(
                                "a",
                                { href: sp_url_pre + val.p_id, target: "_blank" },
                                val.name
                            )
                        ),
                        React.createElement(
                            "div",
                            { className: "sorted" },
                            React.createElement(
                                "span",
                                null,
                                val.sku_desc
                            )
                        )
                    )
                )
            ),
            React.createElement(
                "td",
                { style: { textAlign: "center" } },
                val.price
            ),
            React.createElement(
                "td",
                { style: { textAlign: "center" } },
                val.count
            ),
            React.createElement(
                "td",
                { style: { textAlign: "center" } },
                val.price * 100 * val.count / 100
            )
        );
    });
    return React.createElement(
        "div",
        { className: "order-items" },
        React.createElement(
            "table",
            { width: "100%" },
            React.createElement(
                "thead",
                null,
                React.createElement(
                    "tr",
                    null,
                    React.createElement(
                        "th",
                        { width: "48%" },
                        React.createElement(
                            "span",
                            null,
                            "商品信息"
                        )
                    ),
                    React.createElement(
                        "th",
                        { width: "12%" },
                        React.createElement(
                            "span",
                            null,
                            "单价（元）"
                        )
                    ),
                    React.createElement(
                        "th",
                        { width: "12%" },
                        React.createElement(
                            "span",
                            null,
                            "数量"
                        )
                    ),
                    React.createElement(
                        "th",
                        { width: "28%" },
                        React.createElement(
                            "span",
                            null,
                            "小计（元）"
                        )
                    )
                )
            ),
            React.createElement(
                "tbody",
                null,
                list
            )
        ),
        React.createElement(
            "div",
            { className: "note" },
            React.createElement(
                "span",
                null,
                "给卖家留言:"
            ),
            React.createElement("input", { type: "text", name: "note", value: this.state.note, onChange: this.noteOnChange, placeholder: "选填" })
        )
    );
};

var Pay_type = function Pay_type() {
    var _this2 = this;

    var items = order_items;
    var total_price = this.calcTotalPrice();
    var pay_type = ['zhifubao', 'weixin', 'caifutong'];
    var pay_type_input = pay_type.map(function (val, index) {
        return React.createElement(
            "div",
            { className: "other-type-group" },
            React.createElement(
                "label",
                { className: val != 'zhifubao' ? 'disabled' : null },
                React.createElement("input", { type: "radio", name: "other-pay", checked: _this2.state.other_pay_type == index ? 'checked' : null, disabled: val != 'zhifubao' ? 'disabled' : null, onChange: function onChange() {
                        return _this2.onChangePayType(index);
                    } }),
                React.createElement("div", { className: val })
            )
        );
    });
    return React.createElement(
        "div",
        { className: "pay-order" },
        React.createElement(
            "div",
            { className: "pay-type" },
            React.createElement(
                "div",
                { className: "caption" },
                "选择支付方式支付",
                React.createElement(
                    "em",
                    null,
                    total_price
                ),
                "元"
            ),
            this.state.balance > 0 ? [React.createElement(
                "div",
                { className: "type" },
                React.createElement(
                    "label",
                    null,
                    React.createElement("input", { type: "checkbox", name: "use_balance", checked: this.state.use_balance ? "checked" : null, onClick: this.onChangeUseBalance }),
                    "使用账户余额",
                    this.state.balance,
                    "元"
                ),
                total_price > this.state.balance && this.state.use_balance ? [React.createElement(
                    "div",
                    { className: "detail" },
                    React.createElement(
                        "span",
                        { className: "tip" },
                        "您的余额不足，请选择其他方式支付剩余部分"
                    )
                )] : null
            )] : null,
            total_price <= this.state.balance && this.state.use_balance || isNaN(total_price) ? null : [React.createElement(
                "div",
                { className: "type" },
                React.createElement(
                    "div",
                    { className: "detail" },
                    pay_type_input
                )
            )]
        )
    );
};

var Add_address = function Add_address() {
    var data = this.state.address;
    var tip = "添加新的收货地址";
    if (typeof data.id != 'undefined') {
        tip = "修改收货地址";
    }
    return React.createElement(
        Modal,
        { title: tip, visible: this.state.add_modal, onOk: this.add_modal_ok, onCancel: this.add_modal_cancel },
        React.createElement(
            "div",
            { className: "address-form" },
            React.createElement(
                "div",
                { className: "tip" },
                React.createElement(
                    "span",
                    null,
                    "均为必填项"
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement(
                    "div",
                    { className: "l" },
                    React.createElement(
                        "label",
                        null,
                        "所在地区*"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(Cascader, { options: option, style: { width: 300 }, placeholder: "请选择地区", value: data.addressid, onChange: this.addressOnChange })
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement(
                    "div",
                    { className: "l" },
                    React.createElement(
                        "label",
                        null,
                        "详细地址*"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement("textarea", { placeholder: "建议您填写详细收货地址，例如街道名称，门牌号码，楼层和房间号等信息", value: data.detail, onChange: this.detailOnChange })
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement(
                    "div",
                    { className: "l" },
                    React.createElement(
                        "label",
                        null,
                        "邮政编码*"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(Input, { placeholder: "如您不清楚邮递区号，请填写000000", style: { width: 250 }, value: data.postcode, onChange: this.postcodeOnChange })
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement(
                    "div",
                    { className: "l" },
                    React.createElement(
                        "label",
                        null,
                        "收货人姓名*"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(Input, { placeholder: "长度不超过25个字符", style: { width: 250 }, value: data.contactname, onChange: this.contactnameOnChange })
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement(
                    "div",
                    { className: "l" },
                    React.createElement(
                        "label",
                        null,
                        "手机号码*"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(Input, { placeholder: "请填写手机号码", style: { width: 250 }, value: data.phone, onChange: this.phoneOnChange })
                )
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement("div", { className: "l" }),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(
                        Checkbox,
                        { defaultChecked: false, checked: data.checked, onChange: this.checkOnChange },
                        "设置为默认地址"
                    )
                )
            )
        )
    );
};

var Total_detail = function Total_detail() {
    var discount = 0; //优惠
    var total_price = this.calcTotalPrice();
    var order_price = this.calcOrPrice();
    var _state = this.state;
    var redEnvelope = _state.redEnvelope;
    var coupon = _state.coupon;

    var coupon_options = [React.createElement(
        Option,
        { value: "0" },
        "不使用优惠券"
    )];
    coupon.forEach(function (val) {
        if (order_price >= val.use_condition) {
            coupon_options.push(React.createElement(
                Option,
                { value: val.id },
                val.name
            ));
        }
    });
    var redEnvelope_options = [React.createElement(
        Option,
        { value: "0" },
        "不使用红包"
    )];
    redEnvelope.forEach(function (val) {
        if (order_price >= val.use_condition) {
            redEnvelope_options.push(React.createElement(
                Option,
                { value: val.id },
                val.name
            ));
        }
    });
    var store_youhui = this.state.store_youhui;
    var youhui = this.state.youhui;
    return React.createElement(
        "div",
        null,
        React.createElement(
            "div",
            { className: "youhui" },
            React.createElement(
                "div",
                { className: "youhui-group" },
                React.createElement(
                    "div",
                    { className: "fl" },
                    React.createElement(
                        "label",
                        null,
                        "使用优惠券："
                    ),
                    React.createElement(
                        Select,
                        { defaultValue: "不使用优惠券", onChange: this.onChangeCoupon, style: { width: 150 } },
                        coupon_options
                    )
                ),
                React.createElement(
                    "div",
                    { className: "youhui-discount fr" },
                    "-",
                    youhui.coupon
                )
            ),
            React.createElement(
                "div",
                { className: "youhui-group" },
                React.createElement(
                    "div",
                    { className: "fl" },
                    React.createElement(
                        "label",
                        null,
                        "使用红包："
                    ),
                    React.createElement(
                        Select,
                        { defaultValue: "不使用红包", onChange: this.onChangeRedEnvelope, style: { width: 150 } },
                        redEnvelope_options
                    )
                ),
                React.createElement(
                    "div",
                    { className: "youhui-discount fr" },
                    "-",
                    youhui.redEnvelope
                )
            ),
            React.createElement(
                "div",
                { className: "youhui-group" },
                React.createElement(
                    "div",
                    { className: "fl" },
                    React.createElement(
                        "label",
                        null,
                        "店铺优惠："
                    )
                ),
                React.createElement(
                    "div",
                    { className: "youhui-discount fr" },
                    "-",
                    store_youhui.discount_money,
                    store_youhui.free_shipping ? '(包邮)' : null
                )
            )
        ),
        React.createElement(
            "div",
            { className: "order-total" },
            React.createElement(
                "div",
                { className: "fr" },
                React.createElement(
                    "dl",
                    null,
                    React.createElement(
                        "dt",
                        null,
                        "运费："
                    ),
                    React.createElement(
                        "dd",
                        null,
                        store_youhui.free_shipping ? this.state.freight_cost : '包邮'
                    )
                ),
                React.createElement(
                    "dl",
                    null,
                    React.createElement(
                        "dt",
                        null,
                        "优惠："
                    ),
                    React.createElement(
                        "dd",
                        null,
                        this.state.total_youhui
                    )
                ),
                React.createElement(
                    "dl",
                    { className: "total-price" },
                    React.createElement(
                        "dt",
                        null,
                        "订单总价："
                    ),
                    React.createElement(
                        "dd",
                        null,
                        total_price
                    )
                )
            )
        )
    );
};

var Confirm = React.createClass({
    displayName: "Confirm",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    getInitialState: function getInitialState() {
        var data = this.props.data;
        return { address: {}, data: data, add_modal: false, pay_confirm_modal: false, note: "", freight_cost: 0,
            use_balance: true, balance: wallet, //余额
            other_pay_type: 0, //其他支付方式：0支付宝 1微信 2财付通
            redEnvelope: [], //红包
            coupon: [], //优惠券
            youhui: { coupon: 0, coupon_id: 0, redEnvelope: 0, redEnvelope_id: 0 },
            total_youhui: 0,
            store_youhui: { //店铺优惠
                discount_money: 0,
                free_shipping: false
            }
        };
    },
    calcTotalYouhui: function calcTotalYouhui() {
        //计算优惠
        var total_price = this.calcTotalPrice();
        if (isNaN(total_price)) return;

        //计算总价
        var or_price = this.calcOrPrice();

        var youhui = this.state.youhui;

        var store_youhui = this.state.store_youhui; //店铺优惠
        var store_info = order_info.store_info;
        var time = Date.parse(new Date());
        if (parseInt(store_info.start_time) * 1000 <= time && time <= parseInt(store_info.end_time) * 1000) {
            if (or_price >= parseFloat(store_info.condition)) {
                store_youhui.discount_money = parseFloat(store_info.discount_money) > 0 ? parseFloat(store_info.discount_money) : 0;
                if (parseInt(store_info.free_shipping)) {
                    //包邮;
                    store_youhui.free_shipping = true;
                    this.setState({ freight_cost: 0 });
                }
                this.setState({ store_youhui: store_youhui });
            }
        }
        var total_youhui = store_youhui.discount_money + youhui.coupon + youhui.redEnvelope;
        this.setState({ total_youhui: total_youhui });
    },
    calcTotalPrice: function calcTotalPrice() {
        //计算总价
        var freight_cost = this.state.freight_cost;
        if (isNaN(freight_cost)) return "计算中...";

        var or_price = this.calcOrPrice();

        var youhui = this.state.youhui;

        var store_youhui = this.state.store_youhui; //店铺优惠

        var total_youhui = store_youhui.discount_money + youhui.coupon + youhui.redEnvelope;
        var total_price = or_price + freight_cost - total_youhui;

        if (total_price < 0) total_price = 0.01;

        return total_price;
    },
    calcOrPrice: function calcOrPrice() {
        //计算订单原价
        var or_price = 0;
        order_items.forEach(function (val) {
            var discount = 0; //优惠
            or_price += val.price * 100 * val.count - discount * 100;
        });
        or_price = or_price / 100;
        return or_price;
    },
    addressOnChange: function addressOnChange(value, selectedOptions) {
        var data = this.state.address;
        data.addressid = value;
        data.address = selectedOptions.map(function (val) {
            return val.label;
        });
        this.onEdit(data);
    },
    detailOnChange: function detailOnChange(event) {
        var data = this.state.address;
        data.detail = event.target.value;
        this.onEdit(data);
    },
    postcodeOnChange: function postcodeOnChange(event) {
        var data = this.state.address;
        data.postcode = event.target.value;
        this.onEdit(data);
    },
    contactnameOnChange: function contactnameOnChange(event) {
        var data = this.state.address;
        data.contactname = event.target.value.substr(0, 25);
        this.onEdit(data);
    },
    phoneOnChange: function phoneOnChange(event) {
        if (!isNaN(event.target.value)) {
            //手机号只能输入数字
            var data = this.state.address;
            data.phone = event.target.value;
            this.onEdit(data);
        }
    },
    checkOnChange: function checkOnChange(event) {
        var data = this.state.address;
        data.checked = event.target.checked;
        this.onEdit(data);
    },
    onEdit: function onEdit(data) {
        this.setState({ address: data });
    },
    noteOnChange: function noteOnChange(event) {
        var note = this.state.note;
        note = event.target.value;
        this.setState({ note: note });
    },
    componentDidMount: function componentDidMount() {

        this.update();
        this.calcTotalYouhui();
    },
    update: function update() {
        this.setState({ loading: true });
        var url = getBaseUrl() + '/mengwu/mengwuApi/address_query';
        var data = {};
        _post(url, jQuery.param(data), { data_back: this.update_response });

        this.getCoupon();
        this.getRedEnvelope();
    },
    getCoupon: function getCoupon() {
        //获取优惠券
        var url = getBaseUrl() + '/mengwu/mengwuApi/coupon_query';

        var data = {
            is_use: 0,
            is_expire: 0
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ coupon: data.info });
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    } else {
                        message.error(data.info);
                        this.setState({ loading: false });
                    }
                }
            }.bind(this) });
    },
    getRedEnvelope: function getRedEnvelope() {
        //获取红包
        var url = getBaseUrl() + '/mengwu/mengwuApi/redEnvelope_query';

        var data = {
            is_use: 0,
            is_expire: 0
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ redEnvelope: data.info });
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    } else {
                        message.error(data.info);
                        this.setState({ loading: false });
                    }
                }
            }.bind(this) });
    },
    onChangeCoupon: function onChangeCoupon(value) {
        if (value == 0) {
            var youhui = this.state.youhui;
            youhui.coupon = 0;
            youhui.coupon_id = 0;
            this.setState({ youhui: youhui });
            this.calcTotalYouhui();
            return;
        }
        var coupon = _.find(this.state.coupon, ['id', value]);
        if (coupon) {
            var _youhui = this.state.youhui;
            _youhui.coupon = parseFloat(coupon.money) > 0 ? parseFloat(coupon.money) : 0;
            _youhui.coupon_id = value;
            this.setState({ youhui: _youhui });
            this.calcTotalYouhui();
        }
    },
    onChangeRedEnvelope: function onChangeRedEnvelope(value) {
        if (value == 0) {
            var youhui = this.state.youhui;
            youhui.redEnvelope = 0;
            youhui.redEnvelope_id = 0;
            this.setState({ youhui: youhui });
            this.calcTotalYouhui();
            return;
        }
        var redEnvelope = _.find(this.state.redEnvelope, ['id', value]);
        if (redEnvelope) {
            var _youhui2 = this.state.youhui;
            _youhui2.redEnvelope = parseFloat(redEnvelope.money) > 0 ? parseFloat(redEnvelope.money) : 0;
            _youhui2.redEnvelope_id = value;
            this.setState({ youhui: _youhui2 });
            this.calcTotalYouhui();
        }
    },
    update_response: function update_response(data) {
        var _this3 = this;

        this.setState({ loading: false });
        if (data.status) {
            (function () {
                var address = data.info;
                var d = false;
                address.forEach(function (val, index) {
                    if (val.default == 1) {
                        d = true;
                        val.selected = true;
                        address.splice(index, 1);
                        address.splice(0, 0, val);
                    }
                });
                if (!d && address.length > 0) address[0].selected = true;
                _this3.setState({ data: address });
                _this3.calcFreightFee();
            })();
        } else {
            this.jump(data);
        }
    },
    jump: function jump(data) {
        if (typeof data.info.redirect != "undefined") {
            message.error(data.info.info);
            setTimeout(function () {
                window.location.href = data.info.redirect;
            }, 1000);
        }
    },
    addressAdd: function addressAdd() {
        this.setState({ address: { detail: "" }, add_modal: true });
    },
    add_modal_ok: function add_modal_ok() {
        var address = this.state.address;
        if (typeof address.addressid == 'undefined' || address.addressid.length == 0) {
            message.error('请选择所在地区');
            return;
        }
        if (typeof address.detail == 'undefined' || address.detail == "") {
            message.error('请填写详细地址');
            return;
        }
        if (typeof address.postcode == 'undefined' || address.postcode == "") {
            message.error('请填写邮政编码');
            return;
        }
        if (typeof address.contactname == 'undefined' || address.contactname == "") {
            message.error('请填写收货人姓名');
            return;
        }
        if (typeof address.phone == 'undefined' || address.phone == "") {
            message.error('请填写手机号码');
            return;
        }

        var data = {
            detailinfo: typeof address.detail == 'undefined' ? "" : address.detail,
            contactname: address.contactname,
            mobile: address.phone,
            postal_code: address.postcode,
            default: typeof address.checked == 'undefined' || address.checked == false ? 0 : 1
        };

        address.addressid.forEach(function (val, index) {

            switch (index) {
                case 0:
                    data.province = address.address[0];
                    data.provinceid = val;
                    break;
                case 1:
                    data.city = address.address[1];
                    data.cityid = val;
                    break;
                case 2:
                    data.area = address.address[2];
                    data.areaid = val;
                    break;
            }
        });

        this.setState({ add_modal: false, loading: true });
        var url = getBaseUrl();

        if (typeof address.id != 'undefined') {
            //编辑地址
            data.id = address.id;
            url += '/mengwu/mengwuApi/address_update';
        } else {
            //添加新地址
            url += '/mengwu/mengwuApi/address_add';
        }

        _post(url, jQuery.param(data), { data_back: this.save_response });
    },
    save_response: function save_response(data) {
        if (data.status) {
            this.update();
        } else {
            this.jump(data);
            message.error('保存失败');
            this.setState({ loading: false });
        }
    },
    add_modal_cancel: function add_modal_cancel() {
        this.setState({ add_modal: false });
    },
    edit_address: function edit_address(data) {
        if (typeof data.id == "undefined") {
            message.error("地址数据异常,无法编辑，请刷新后重试！");
            return;
        }
        if (data.default == 1) data.checked = true;
        this.setState({ address: data, add_modal: true });
    },
    selectAddress: function selectAddress(id) {
        var c = false;
        var data = this.state.data.map(function (val) {
            if (val.id == id) {
                if (!val.selected) c = true;
                val.selected = true;
            } else {
                val.selected = false;
            }
            return val;
        });
        if (c) {
            this.setState({ data: data });
            this.calcFreightFee();
        }
    },
    calcFreightFee: function calcFreightFee() {
        //计算运费
        var from = order_info.from ? order_info.from : 'unknown';
        if (from == 'unknown') {
            console.log('运费计算失败');
            return;
        }
        this.setState({ freight_cost: '计算中...' });
        var url = getBaseUrl() + '/mengwu/mengwuApi/order_freight_cost';
        var address_id = 0;
        var cart_ids = [];
        this.state.data.forEach(function (val) {
            if (val.selected) address_id = val.id;
        });
        if (address_id == 0) {
            console.log('请选择地址');
        }
        cart_ids = order_items.map(function (val) {
            return val.id;
        });

        var data = {
            from: from,
            address_id: address_id
        };
        if (from == 'spcart') {
            data.cart_ids = cart_ids.join(',');
        }
        if (from == 'buy') {
            if (!order_info.order_items || !order_info.order_items[0]) {
                console.log('运费计算失败,商品信息不存在');
                this.setState({ freight_cost: 0 });
                return;
            }
            data.pid = order_info.order_items[0].p_id;
            data.count = order_info.order_items[0].count;
        }

        _post(url, jQuery.param(data), { data_back: this.calcFreightFee_res });
    },
    calcFreightFee_res: function calcFreightFee_res(data) {
        if (data.status) {
            this.setState({ freight_cost: parseFloat(data.info) });
        } else {
            this.jump(data);
        }
    },
    onChangeUseBalance: function onChangeUseBalance(event) {
        this.setState({ use_balance: event.target.checked });
    },
    onChangePayType: function onChangePayType(type) {
        //支付方式改变
        this.setState({ other_pay_type: type });
    },
    confirmAndPay: function confirmAndPay() {
        //生成订单
        var url = getBaseUrl() + '/mengwu/order/order_add';
        var note = this.state.note;
        var address_id = 0;
        var cart_ids = [];
        var youhui = this.state.youhui;
        var money = 0;
        var balance = this.state.balance > 0 ? this.state.balance : 0;
        var total_price = this.calcTotalPrice();
        if (isNaN(total_price)) {
            message.error('请稍等~~');
            return;
        }
        cart_ids = order_items.map(function (val) {
            return val.id;
        });
        this.state.data.forEach(function (val) {
            if (val.selected) address_id = val.id;
        });
        if (address_id == 0) {
            message.error('请选择收货地址！');
            return;
        }

        this.setState({ loading: true });

        if (this.state.use_balance) {
            if (balance >= total_price) {
                money = total_price;
            } else {
                money = balance;
            }
        }

        var data = {
            note: note,
            address_id: address_id,
            coupon_id: youhui.coupon_id,
            red_id: youhui.redEnvelope_id,
            data: JSON.stringify(order_info),
            money: money
        };

        _post(url, jQuery.param(data), { data_back: this.orderAdd_res });
    },
    orderAdd_res: function orderAdd_res(data) {
        if (data.status) {
            if (data.info.total_price > 0) {
                //打开新窗口支付
                this.setState({ loading: false, pay_confirm_modal: true, pay_info: data.info });
                console.log('打开新窗口支付');
                var form = $('<form></form>');
                var action = getBaseUrl() + '/mengwu/order/jump2Pay';
                form.attr({ action: action, method: 'post', target: '_blank' });
                var pay_info_input = $('<input type="text" name="pay_info" />');
                pay_info_input.attr('value', JSON.stringify(data.info));
                var type_input = $('<input type="text" name="type" />');
                type_input.attr('value', this.other_pay_type);
                form.append(pay_info_input, type_input);
                $('body').append(form);
                form.submit().remove();
            } else {
                this.jump_order_detail();
            }
        } else {
            this.jump(data);
            message.error(data.info);
            this.setState({ loading: false });
        }
    },
    confirmPay: function confirmPay() {
        var pay_info = this.state.pay_info;
        var form = $('<form></form>');
        var action = getBaseUrl() + '/mengwu/order/jump2Pay';
        form.attr({ action: action, method: 'post', target: '_blank' });
        var pay_info_input = $('<input type="text" name="pay_info" />');
        pay_info_input.attr('value', JSON.stringify(pay_info));
        var type_input = $('<input type="text" name="type" />');
        type_input.attr('value', this.other_pay_type);
        form.append(pay_info_input, type_input);
        $('body').append(form);
        form.submit().remove();
    },
    jump_order_detail: function jump_order_detail() {
        window.location.href = getBaseUrl() + '/mengwu/user/order';
    },
    render: function render() {
        var address_url = getBaseUrl() + '/mengwu/user/address';
        var add = Add_address.bind(this)();
        var address_list = Address.bind(this)();
        var pay_type = Pay_type.bind(this)();
        var items = Order_Items.bind(this)();
        var total_detail = Total_detail.bind(this)();
        var selected_addr = {};
        var total_price = this.calcTotalPrice();
        var balance = this.state.balance; //余额
        this.state.data.forEach(function (val) {
            if (val.selected) selected_addr = val;
        });
        var pay_type_desc = ['支付宝', '微信', '财付通'];
        var pay_desc = '';
        if (this.state.use_balance && total_price <= balance) {
            pay_desc = '使用余额支付';
        } else {
            pay_desc = "使用" + pay_type_desc[this.state.other_pay_type] + '支付';
        }

        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            add,
            React.createElement(
                "div",
                { className: "top" },
                React.createElement(
                    "h1",
                    null,
                    "确认订单"
                ),
                React.createElement("div", { className: "progress" })
            ),
            React.createElement(
                "div",
                { className: "confirm-inner" },
                React.createElement(
                    "div",
                    { className: "step" },
                    React.createElement(
                        "h4",
                        null,
                        "选择收货地址"
                    ),
                    " ",
                    React.createElement(
                        "a",
                        { href: address_url, target: "_blank" },
                        "管理收货地址"
                    )
                ),
                address_list,
                React.createElement(
                    "div",
                    { className: "step nob" },
                    React.createElement(
                        "h4",
                        null,
                        "确认订单信息"
                    )
                ),
                items,
                total_detail,
                React.createElement(
                    "div",
                    { className: "step" },
                    React.createElement(
                        "h4",
                        null,
                        "选择支付方式"
                    )
                ),
                pay_type,
                React.createElement(
                    "div",
                    { className: "confirm-info-wrap" },
                    React.createElement(
                        "div",
                        { className: "confirm-info" },
                        React.createElement(
                            "p",
                            null,
                            pay_desc,
                            React.createElement(
                                "em",
                                null,
                                total_price
                            ),
                            "元"
                        ),
                        React.createElement(
                            "p",
                            null,
                            "寄送至：",
                            React.createElement(
                                "span",
                                null,
                                selected_addr.address,
                                " ",
                                selected_addr.detail
                            )
                        ),
                        React.createElement(
                            "p",
                            null,
                            "收货人：",
                            React.createElement(
                                "span",
                                null,
                                selected_addr.contactname,
                                " ",
                                selected_addr.phone
                            )
                        )
                    )
                ),
                React.createElement(
                    Modal,
                    { title: "确认支付", visible: this.state.pay_confirm_modal, okText: "已支付", cancelText: "前往支付", closable: false, style: { top: 300 }, onOk: this.jump_order_detail, onCancel: this.confirmPay },
                    React.createElement(
                        "p",
                        null,
                        "请在新打开的页面中完成支付"
                    ),
                    React.createElement(
                        "p",
                        null,
                        "1.如果没有跳出新窗口，请检查浏览器是否允许弹出新窗口"
                    ),
                    React.createElement(
                        "p",
                        null,
                        "2.点击前往支付可重新支付"
                    )
                ),
                React.createElement(
                    "div",
                    { className: "oper-btn clear text-right" },
                    React.createElement(
                        "a",
                        { href: "javascript:void(0);", className: "btn btn-confirm", onClick: this.confirmAndPay },
                        "确认并支付"
                    )
                )
            )
        );
    }
});

ReactDOM.render(React.createElement(Confirm, null), document.getElementById('order-confirm'));

//# sourceMappingURL=common.js.map