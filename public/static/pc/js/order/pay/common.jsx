const { Modal } = antd;
const Pay = React.createClass({
    getInitialState(){
        return({
            other_pay_type: 0,
            pay_confirm_modal: false
        });
    },
    jump_order_detail(){
        window.location.href = getBaseUrl() + '/mengwu/user/order';
    },
    onChangePayType(type){
        this.setState({other_pay_type: type});
    },
    confirmPay(){
        let form = $('<form></form>');
        let action = getBaseUrl() + '/mengwu/order/jump2Pay';
        form.attr({action:action,method:'post',target:'_blank'});
        let info_input = $('<input type="text" name="pay_info" />');
        info_input.attr('value',JSON.stringify(pay_info));
        let type_input = $('<input type="text" name="pay_type" />');
        type_input.attr('value',this.state.other_pay_type);
        form.append(info_input,type_input);
        $('body').append(form);
        form.submit().remove();
        this.setState({pay_confirm_modal: true});
    },
    render(){
        const total_price = pay_info.total_price ? pay_info.total_price : 'error';
        const pay_type = ['zhifubao','weixin','caifutong'];
        const pay_type_input = pay_type.map((val,index)=>
            <div className="other-type-group"><label className={val!='zhifubao' ? 'disabled' : null}><input type="radio" name="other-pay" checked={this.state.other_pay_type==index?'checked':null} disabled={val!='zhifubao' ? 'disabled' : null} onChange={()=>this.onChangePayType(index)} /><div className={val}></div></label></div>
        );
        return(
            <div className="pay-order">
                <div className="pay-type">
                    <div className="caption">
                        选择支付方式支付<em>{total_price}</em>元
                    </div>
                    <div className="type">
                        <div className="detail">
                            {pay_type_input}
                        </div>
                    </div>
                </div>
                <Modal title="确认支付" visible={this.state.pay_confirm_modal} okText="已支付" cancelText="支付遇到问题" onCancel={this.jump_order_detail} closable={false} style={{top: 300}} onOk={this.jump_order_detail}>
                    <p>请在新打开的页面中完成支付</p>
                </Modal>
                <div className="oper-btn clear text-right">
                    <a href="javascript:void(0);" className="btn btn-confirm" onClick={this.confirmPay}>确认支付</a>
                </div>
            </div>
        );
    }
});

ReactDOM.render(
    <Pay/>,
    document.getElementById('order_pay')
);