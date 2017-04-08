"use strict";

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var _mengwu = mengwu;
var option = _mengwu.address_json;
var _antd = antd;
var Cascader = _antd.Cascader;
var Input = _antd.Input;
var Checkbox = _antd.Checkbox;
var message = _antd.message;
var Spin = _antd.Spin;
var Popconfirm = _antd.Popconfirm;

var Aaddress_editor = React.createClass({
    displayName: "Aaddress_editor",
    addressOnChange: function addressOnChange(value, selectedOptions) {
        var data = this.props.data;
        data.addressid = value;
        data.address = selectedOptions.map(function (val) {
            return val.label;
        });
        this.props.onEdit(data);
    },
    detailOnChange: function detailOnChange(event) {
        var data = this.props.data;
        data.detail = event.target.value;
        this.props.onEdit(data);
    },
    postcodeOnChange: function postcodeOnChange(event) {
        var data = this.props.data;
        data.postcode = event.target.value;
        this.props.onEdit(data);
    },
    contactnameOnChange: function contactnameOnChange(event) {
        var data = this.props.data;
        data.contactname = event.target.value.substr(0, 25);
        this.props.onEdit(data);
    },
    phoneOnChange: function phoneOnChange(event) {
        if (!isNaN(event.target.value)) {
            //手机号只能输入数字
            var data = this.props.data;
            data.phone = event.target.value;
            this.props.onEdit(data);
        }
    },
    checkOnChange: function checkOnChange(event) {
        var data = this.props.data;
        data.checked = event.target.checked;
        this.props.onEdit(data);
    },
    render: function render() {

        var data = this.props.data;
        var tip = "新增收货地址";
        if (typeof data.id != 'undefined') {
            tip = "修改收货地址";
        }
        return React.createElement(
            "div",
            { className: "address-form" },
            React.createElement(
                "div",
                { className: "tip" },
                React.createElement(
                    "h4",
                    null,
                    tip
                ),
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
            ),
            React.createElement(
                "div",
                { className: "form-group" },
                React.createElement("div", { className: "l" }),
                React.createElement(
                    "div",
                    { className: "r" },
                    React.createElement(
                        "a",
                        { className: "btn save", href: "javascript:void(0);", onClick: this.props.save },
                        "保存"
                    )
                )
            )
        );
    }
});

var Address_list = React.createClass({
    displayName: "Address_list",
    onEdit: function onEdit() {
        var data = {};
        _.assign(data, this.props.data);
        if (this.props.data.default == 1) data.checked = true;
        this.props.onEdit(data);
    },
    onDelete: function onDelete() {
        this.props.delete(this.props.data.id);
    },
    render: function render() {
        var data = this.props.data;
        return React.createElement(
            "tr",
            { className: "list" },
            React.createElement(
                "td",
                { className: "contactname" },
                data.contactname
            ),
            React.createElement(
                "td",
                { className: "address" },
                data.address
            ),
            React.createElement(
                "td",
                { className: "detail" },
                data.detail
            ),
            React.createElement(
                "td",
                { className: "postcode" },
                data.postcode
            ),
            React.createElement(
                "td",
                { className: "phone" },
                data.phone
            ),
            React.createElement(
                "td",
                _defineProperty({ className: "oper" }, "className", data.default == 1 ? 'default' : ""),
                React.createElement(
                    "a",
                    { href: "javascript:void(0);", onClick: this.onEdit },
                    "修改"
                ),
                "|",
                React.createElement(
                    Popconfirm,
                    { title: "确定要删除这个地址吗？", onConfirm: this.onDelete },
                    React.createElement(
                        "a",
                        { href: "javascript:void(0);" },
                        "删除"
                    )
                )
            )
        );
    }
});

var address_list = [{ id: 1, contactname: "艾米", address: "北京市市辖区东城区", addressid: ['110000', '110100', '110101'], detail: "万亚金沙湖1号2幢1515", postcode: "310018", phone: "18612345662" }, { id: 2, contactname: "艾米", address: "北京市市辖区东城区", addressid: ['110000', '110100', '110101'], detail: "万亚金沙湖1号2幢1515", postcode: "310018", phone: "18612345662" }];

var Address = React.createClass({
    displayName: "Address",
    getDefaultProps: function getDefaultProps() {
        return { data: [] };
    },
    getInitialState: function getInitialState() {
        var data = this.props.data;
        return { address: {}, data: data };
    },
    componentDidMount: function componentDidMount() {

        this.update();
    },
    onEdit: function onEdit(data) {
        this.setState({ address: data });
    },
    addAddress: function addAddress() {
        this.setState({ address: { detail: "" } });
        $('html, body').animate({
            scrollTop: 0
        }, 200);
    },
    update: function update() {
        this.setState({ loading: true });
        var url = getBaseUrl() + '/mengwu/mengwuApi/address_query';
        var data = {};
        _post(url, jQuery.param(data), { data_back: this.update_response });
    },
    update_response: function update_response(data) {
        this.setState({ loading: false });
        if (data.status) {
            this.setState({ data: data.info });
        } else {
            this.jump(data);
        }
    },
    delete: function _delete(id) {
        this.setState({ loading: true });
        var url = getBaseUrl() + '/mengwu/mengwuApi/address_delete';
        var data = {
            id: id
        };
        _post(url, jQuery.param(data), { data_back: this.delete_response });
    },
    delete_response: function delete_response(data) {
        if (data.status) {
            this.update();
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
    save: function save() {
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

        this.setState({ loading: true });
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
            message.success('保存成功');
            this.update();
        } else {
            this.jump(data);
            message.error('保存失败');
            this.setState({ loading: false });
        }
    },
    render: function render() {
        var list = this.state.data.map(function (val) {
            return React.createElement(Address_list, { data: val, onEdit: this.onEdit, "delete": this.delete });
        }.bind(this));
        if (list.length == 0) list = React.createElement(
            "tr",
            { className: "list" },
            React.createElement(
                "td",
                { colSpan: "6" },
                "暂无收货地址"
            )
        );
        return React.createElement(
            "div",
            null,
            React.createElement(
                Spin,
                { spinning: this.state.loading },
                React.createElement(Aaddress_editor, { data: this.state.address, onEdit: this.onEdit, save: this.save }),
                React.createElement(
                    "div",
                    { className: "address-list" },
                    React.createElement(
                        "div",
                        { className: "tip" },
                        React.createElement(
                            "h4",
                            null,
                            "已保存的有效地址"
                        ),
                        React.createElement(
                            "a",
                            { href: "javascript:void(0);", onClick: this.addAddress },
                            "添加新地址"
                        )
                    ),
                    React.createElement(
                        "table",
                        { width: "100%" },
                        React.createElement(
                            "thead",
                            null,
                            React.createElement(
                                "tr",
                                { className: "head" },
                                React.createElement(
                                    "th",
                                    { className: "contactname" },
                                    "收货人"
                                ),
                                React.createElement(
                                    "th",
                                    { className: "address" },
                                    "所在地区"
                                ),
                                React.createElement(
                                    "th",
                                    { className: "detail" },
                                    "详细地址"
                                ),
                                React.createElement(
                                    "th",
                                    { className: "postcode" },
                                    "邮编"
                                ),
                                React.createElement(
                                    "th",
                                    { className: "phone" },
                                    "手机"
                                ),
                                React.createElement(
                                    "th",
                                    { className: "oper" },
                                    "操作"
                                )
                            )
                        ),
                        React.createElement(
                            "tbody",
                            null,
                            list
                        )
                    )
                )
            )
        );
    }
});

ReactDOM.render(React.createElement(Address, null), document.getElementById('address'));

//# sourceMappingURL=common.js.map