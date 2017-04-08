'use strict';

var _antd = antd;
var Modal = _antd.Modal;

var Pay = React.createClass({
    displayName: 'Pay',
    getInitialState: function getInitialState() {
        return {
            other_pay_type: 0,
            pay_confirm_modal: false
        };
    },
    jump_order_detail: function jump_order_detail() {
        window.location.href = getBaseUrl() + '/mengwu/user/order';
    },
    onChangePayType: function onChangePayType(type) {
        this.setState({ other_pay_type: type });
    },
    confirmPay: function confirmPay() {
        var form = $('<form></form>');
        var action = getBaseUrl() + '/mengwu/order/jump2Pay';
        form.attr({ action: action, method: 'post', target: '_blank' });
        var info_input = $('<input type="text" name="pay_info" />');
        info_input.attr('value', JSON.stringify(pay_info));
        var type_input = $('<input type="text" name="pay_type" />');
        type_input.attr('value', this.state.other_pay_type);
        form.append(info_input, type_input);
        $('body').append(form);
        form.submit().remove();
        this.setState({ pay_confirm_modal: true });
    },
    render: function render() {
        var _this = this;

        var total_price = pay_info.total_price ? pay_info.total_price : 'error';
        var pay_type = ['zhifubao', 'weixin', 'caifutong'];
        var pay_type_input = pay_type.map(function (val, index) {
            return React.createElement(
                'div',
                { className: 'other-type-group' },
                React.createElement(
                    'label',
                    { className: val != 'zhifubao' ? 'disabled' : null },
                    React.createElement('input', { type: 'radio', name: 'other-pay', checked: _this.state.other_pay_type == index ? 'checked' : null, disabled: val != 'zhifubao' ? 'disabled' : null, onChange: function onChange() {
                            return _this.onChangePayType(index);
                        } }),
                    React.createElement('div', { className: val })
                )
            );
        });
        return React.createElement(
            'div',
            { className: 'pay-order' },
            React.createElement(
                'div',
                { className: 'pay-type' },
                React.createElement(
                    'div',
                    { className: 'caption' },
                    '选择支付方式支付',
                    React.createElement(
                        'em',
                        null,
                        total_price
                    ),
                    '元'
                ),
                React.createElement(
                    'div',
                    { className: 'type' },
                    React.createElement(
                        'div',
                        { className: 'detail' },
                        pay_type_input
                    )
                )
            ),
            React.createElement(
                Modal,
                { title: '确认支付', visible: this.state.pay_confirm_modal, okText: '已支付', cancelText: '支付遇到问题', onCancel: this.jump_order_detail, closable: false, style: { top: 300 }, onOk: this.jump_order_detail },
                React.createElement(
                    'p',
                    null,
                    '请在新打开的页面中完成支付'
                )
            ),
            React.createElement(
                'div',
                { className: 'oper-btn clear text-right' },
                React.createElement(
                    'a',
                    { href: 'javascript:void(0);', className: 'btn btn-confirm', onClick: this.confirmPay },
                    '确认支付'
                )
            )
        );
    }
});

ReactDOM.render(React.createElement(Pay, null), document.getElementById('order_pay'));

//# sourceMappingURL=common.js.map