const { address_json: option } = mengwu;
const { Modal,Cascader,Input,Checkbox,message,Spin,Select } = antd;
const Address = function(){
    let list = this.state.data.map(function(val){
        let cl = val.selected==true ? "address-list selected" : "address-list";
        let data = {};
        _.assign(data, val);
        return(
            <li className={cl} onClick={()=>this.selectAddress(data.id)}>
                <div>
                    <p><em>{val.address}</em> ({val.contactname} 收) <span>默认</span></p>
                    <p>{val.detail}</p>
                    <p>{val.phone}</p>
                    <p><a href="javascript:void(0);" onClick={()=>this.edit_address(data)}>修改</a></p>
                </div>
            </li>
        );
    }.bind(this));
    return(
        <div className="address-wrap">
            <ul>
                {list}
            </ul>
            <a className="btn add-address" href="javascript:void(0);" onClick={this.addressAdd}>使用新地址</a>
        </div>
    );
};

const Order_Items = function(){
    let sp_url_pre = getBaseUrl() + '/mengwu/spdetail/index/id/';
    let list = order_items.map(function(val){
        return(
            <tr className="order-item">
                <td>
                    <div className="item-info">
                        <div className="sp-img">
                            <a href={sp_url_pre+val.p_id} target="_blank"><img src={val.icon_url}/></a>
                        </div>
                        <div className="introduce">
                            <div className="caption">
                                <a href={sp_url_pre+val.p_id} target="_blank">{val.name}</a>
                            </div>
                            <div className="sorted"><span>{val.sku_desc}</span></div>
                        </div>
                    </div>
                </td>
                <td style={{textAlign: "center"}}>{val.price}</td>
                <td style={{textAlign: "center"}}>{val.count}</td>
                <td style={{textAlign: "center"}}>{val.price *100 * val.count /100}</td>
            </tr>
        );
    });
    return(
        <div className="order-items">
            <table width="100%">
                <thead>
                <tr>
                    <th width="48%"><span>商品信息</span></th>
                    <th width="12%"><span>单价（元）</span></th>
                    <th width="12%"><span>数量</span></th>
                    <th width="28%"><span>小计（元）</span></th>
                </tr>
                </thead>
                <tbody>
                {list}
                </tbody>
            </table>
            <div className="note">
                <span>给卖家留言:</span><input type="text" name="note" value={this.state.note} onChange={this.noteOnChange} placeholder="选填" />
            </div>
        </div>
    );
};

const Pay_type = function(){
    let items = order_items;
    let total_price = this.calcTotalPrice();
    const pay_type = ['zhifubao','weixin','caifutong'];
    const pay_type_input = pay_type.map((val,index)=>
        <div className="other-type-group"><label className={val!='zhifubao' ? 'disabled' : null}><input type="radio" name="other-pay" checked={this.state.other_pay_type==index?'checked':null} disabled={val!='zhifubao' ? 'disabled' : null} onChange={()=>this.onChangePayType(index)} /><div className={val}></div></label></div>
    );
    return (
        <div className="pay-order">
            <div className="pay-type">
                <div className="caption">
                    选择支付方式支付<em>{total_price}</em>元
                </div>
                {this.state.balance>0?[
                    <div className="type">
                        <label><input type="checkbox" name="use_balance" checked={this.state.use_balance ? "checked" : null} onClick={this.onChangeUseBalance} />使用账户余额{this.state.balance}元</label>
                        {total_price > this.state.balance && this.state.use_balance ? [
                            <div className="detail">
                                <span className="tip">您的余额不足，请选择其他方式支付剩余部分</span>
                            </div>
                        ]:null}
                    </div>
                ]:null}
                {total_price <= this.state.balance && this.state.use_balance || isNaN(total_price) ? null : [
                    <div className="type">
                        <div className="detail">
                            {pay_type_input}
                        </div>
                    </div>
                ]}
            </div>
        </div>
    );
};

const Add_address = function(){
    let data = this.state.address;
    let tip = "添加新的收货地址";
    if(typeof(data.id)!='undefined'){
        tip = "修改收货地址";
    }
    return(
        <Modal title={tip} visible={this.state.add_modal} onOk={this.add_modal_ok} onCancel={this.add_modal_cancel}>
            <div className="address-form">
                <div className="tip"><span>均为必填项</span></div>
                <div className="form-group">
                    <div className="l">
                        <label>所在地区*</label>
                    </div>
                    <div className="r">
                        <Cascader options={option} style={{width: 300}} placeholder="请选择地区" value={data.addressid} onChange={this.addressOnChange} />
                    </div>
                </div>
                <div className="form-group">
                    <div className="l">
                        <label>详细地址*</label>
                    </div>
                    <div className="r">
                        <textarea placeholder="建议您填写详细收货地址，例如街道名称，门牌号码，楼层和房间号等信息" value={data.detail} onChange={this.detailOnChange} />
                    </div>
                </div>
                <div className="form-group">
                    <div className="l">
                        <label>邮政编码*</label>
                    </div>
                    <div className="r">
                        <Input placeholder="如您不清楚邮递区号，请填写000000" style={{width:250}} value={data.postcode} onChange={this.postcodeOnChange} />
                    </div>
                </div>
                <div className="form-group">
                    <div className="l">
                        <label>收货人姓名*</label>
                    </div>
                    <div className="r">
                        <Input placeholder="长度不超过25个字符" style={{width:250}} value={data.contactname} onChange={this.contactnameOnChange} />
                    </div>
                </div>
                <div className="form-group">
                    <div className="l">
                        <label>手机号码*</label>
                    </div>
                    <div className="r">
                        <Input placeholder="请填写手机号码" style={{width:250}} value={data.phone} onChange={this.phoneOnChange} />
                    </div>
                </div>
                <div className="form-group">
                    <div className="l"></div>
                    <div className="r">
                        <Checkbox defaultChecked={false} checked={data.checked} onChange={this.checkOnChange} >设置为默认地址</Checkbox>
                    </div>
                </div>
            </div>
        </Modal>
    );
};

const Total_detail = function(){
    let discount = 0; //优惠
    let total_price = this.calcTotalPrice();
    let order_price = this.calcOrPrice();
    const { redEnvelope, coupon} = this.state;
    let coupon_options = [<Option value="0">不使用优惠券</Option>];
    coupon.forEach(function(val){
        if(order_price>=val.use_condition){
            coupon_options.push(
                <Option value={val.id}>{val.name}</Option>
            );
        }
    });
    let redEnvelope_options = [<Option value="0">不使用红包</Option>];
    redEnvelope.forEach(function(val){
        if(order_price>=val.use_condition){
            redEnvelope_options.push(
                <Option value={val.id}>{val.name}</Option>
            );
        }
    });
    let store_youhui = this.state.store_youhui;
    let youhui = this.state.youhui;
    return(
        <div>
            <div className="youhui">
                <div className="youhui-group">
                    <div className="fl">
                        <label>使用优惠券：</label><Select defaultValue="不使用优惠券" onChange={this.onChangeCoupon} style={{ width: 150 }}>{coupon_options}</Select>
                    </div>
                    <div className="youhui-discount fr">-{youhui.coupon}</div>
                </div>
                <div className="youhui-group">
                    <div className="fl">
                        <label>使用红包：</label><Select defaultValue="不使用红包" onChange={this.onChangeRedEnvelope} style={{ width: 150 }}>{redEnvelope_options}</Select>
                    </div>
                    <div className="youhui-discount fr">-{youhui.redEnvelope}</div>
                </div>
                <div className="youhui-group">
                    <div className="fl">
                        <label>店铺优惠：</label>
                    </div>
                    <div className="youhui-discount fr">-{store_youhui.discount_money}{store_youhui.free_shipping?'(包邮)':null}</div>
                </div>
            </div>
            <div className="order-total">
                <div className="fr">
                    <dl>
                        <dt>运费：</dt>
                        <dd>{store_youhui.free_shipping?this.state.freight_cost:'包邮'}</dd>
                    </dl>
                    <dl>
                        <dt>优惠：</dt>
                        <dd>{this.state.total_youhui}</dd>
                    </dl>
                    <dl className="total-price">
                        <dt>订单总价：</dt>
                        <dd>{total_price}</dd>
                    </dl>
                </div>
            </div>
        </div>
    );
};

const Confirm = React.createClass({
    getDefaultProps(){
        return({data: []});
    },
    getInitialState(){
        let data = this.props.data;
        return({address: {},data: data, add_modal:false, pay_confirm_modal:false, note:"", freight_cost: 0,
            use_balance: true, balance: wallet, //余额
            other_pay_type: 0,//其他支付方式：0支付宝 1微信 2财付通
            redEnvelope: [], //红包
            coupon: [], //优惠券
            youhui: {coupon:0,coupon_id:0, redEnvelope:0,redEnvelope_id:0},
            total_youhui: 0,
            store_youhui: { //店铺优惠
                discount_money:0,
                free_shipping:false
            }
        });
    },
    calcTotalYouhui(){
        //计算优惠
        let total_price = this.calcTotalPrice();
        if(isNaN(total_price)) return;

        //计算总价
        let or_price = this.calcOrPrice();

        let youhui = this.state.youhui;

        let store_youhui = this.state.store_youhui; //店铺优惠
        const store_info = order_info.store_info;
        let time = Date.parse(new Date());
        if(parseInt(store_info.start_time)*1000<=time && time<=parseInt(store_info.end_time)*1000){
            if(or_price >=parseFloat(store_info.condition)){
                store_youhui.discount_money = parseFloat(store_info.discount_money)>0?parseFloat(store_info.discount_money):0;
                if(parseInt(store_info.free_shipping)){
                    //包邮;
                    store_youhui.free_shipping = true;
                    this.setState({freight_cost: 0});
                }
                this.setState({store_youhui});
            }
        }
        let total_youhui = store_youhui.discount_money + youhui.coupon + youhui.redEnvelope;
        this.setState({total_youhui});
    },
    calcTotalPrice(){
        //计算总价
        let freight_cost = this.state.freight_cost;
        if(isNaN(freight_cost)) return "计算中...";

        let or_price = this.calcOrPrice();

        let youhui = this.state.youhui;

        let store_youhui = this.state.store_youhui; //店铺优惠

        let total_youhui = store_youhui.discount_money + youhui.coupon + youhui.redEnvelope;
        let total_price = or_price + freight_cost - total_youhui;

        if(total_price < 0)total_price = 0.01;

        return total_price;
    },
    calcOrPrice(){
        //计算订单原价
        let or_price = 0;
        order_items.forEach(function(val){
            let discount = 0; //优惠
            or_price += val.price * 100 * val.count -discount*100;
        });
        or_price = or_price / 100;
        return or_price;
    },
    addressOnChange(value, selectedOptions){
        let data = this.state.address;
        data.addressid = value;
        data.address = selectedOptions.map(function(val){
            return val.label;
        });
        this.onEdit(data);
    },
    detailOnChange(event){
        let data = this.state.address;
        data.detail = event.target.value;
        this.onEdit(data);
    },
    postcodeOnChange(event){
        let data = this.state.address;
        data.postcode = event.target.value;
        this.onEdit(data);
    },
    contactnameOnChange(event){
        let data = this.state.address;
        data.contactname = event.target.value.substr(0,25);
        this.onEdit(data);
    },
    phoneOnChange(event){
        if(!isNaN(event.target.value)){
            //手机号只能输入数字
            let data = this.state.address;
            data.phone = event.target.value;
            this.onEdit(data);
        }
    },
    checkOnChange(event){
        let data = this.state.address;
        data.checked = event.target.checked;
        this.onEdit(data);
    },
    onEdit(data){
        this.setState({address: data});
    },
    noteOnChange(event){
        let note = this.state.note;
        note = event.target.value;
        this.setState({note: note});
    },
    componentDidMount(){

        this.update();
        this.calcTotalYouhui();
    },
    update(){
        this.setState({loading: true});
        let url = getBaseUrl() + '/mengwu/mengwuApi/address_query';
        let data = {};
        _post(url,jQuery.param(data),{data_back: this.update_response});

        this.getCoupon();
        this.getRedEnvelope();
    },
    getCoupon(){
        //获取优惠券
        let url = getBaseUrl() + '/mengwu/mengwuApi/coupon_query';

        let data = {
            is_use: 0,
            is_expire: 0
        };

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({coupon: data.info});
            }else{
                if(typeof data.info.redirect !="undefined"){
                    window.location.href = data.info.redirect;
                }else{
                    message.error(data.info);
                    this.setState({loading: false});
                }
            }

        }.bind(this)});
    },
    getRedEnvelope(){
        //获取红包
        let url = getBaseUrl() + '/mengwu/mengwuApi/redEnvelope_query';

        let data = {
            is_use: 0,
            is_expire: 0
        };

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({redEnvelope: data.info});
            }else{
                if(typeof data.info.redirect !="undefined"){
                    window.location.href = data.info.redirect;
                }else{
                    message.error(data.info);
                    this.setState({loading: false});
                }
            }

        }.bind(this)});
        
    },
    onChangeCoupon(value){
        if(value==0){
            let youhui = this.state.youhui;
            youhui.coupon = 0;
            youhui.coupon_id = 0;
            this.setState({youhui});
            this.calcTotalYouhui();
            return;
        }
        const coupon = _.find(this.state.coupon,['id',value]);
        if(coupon){
            let youhui = this.state.youhui;
            youhui.coupon = parseFloat(coupon.money) > 0 ? parseFloat(coupon.money) : 0;
            youhui.coupon_id = value;
            this.setState({youhui});
            this.calcTotalYouhui();
        }
    },
    onChangeRedEnvelope(value){
        if(value==0){
            let youhui = this.state.youhui;
            youhui.redEnvelope = 0;
            youhui.redEnvelope_id = 0;
            this.setState({youhui});
            this.calcTotalYouhui();
            return;
        }
        const redEnvelope = _.find(this.state.redEnvelope,['id',value]);
        if(redEnvelope){
            let youhui = this.state.youhui;
            youhui.redEnvelope = parseFloat(redEnvelope.money) > 0 ? parseFloat(redEnvelope.money) : 0;
            youhui.redEnvelope_id = value;
            this.setState({youhui});
            this.calcTotalYouhui();
        }
    },
    update_response(data){
        this.setState({loading: false});
        if(data.status){
            let address = data.info;
            let d = false;
            address.forEach(function(val,index){
                if(val.default == 1){
                    d = true;
                    val.selected = true;
                    address.splice(index,1);
                    address.splice(0,0,val);
                }
            });
            if(!d && address.length>0) address[0].selected = true;
            this.setState({data: address});
            this.calcFreightFee();
        }else{
            this.jump(data);
        }
    },
    jump(data){
        if(typeof data.info.redirect !="undefined"){
            message.error(data.info.info);
            setTimeout(function(){
                window.location.href = data.info.redirect;
            },1000);
        }
    },
    addressAdd(){
        this.setState({address: {detail:""},add_modal: true});
    },
    add_modal_ok(){
        let address = this.state.address;
        if(typeof address.addressid == 'undefined' || address.addressid.length == 0){
            message.error('请选择所在地区');
            return;
        }
        if(typeof address.detail == 'undefined' || address.detail == ""){
            message.error('请填写详细地址');
            return;
        }
        if(typeof address.postcode == 'undefined' || address.postcode == ""){
            message.error('请填写邮政编码');
            return;
        }
        if(typeof address.contactname == 'undefined' || address.contactname == ""){
            message.error('请填写收货人姓名');
            return;
        }
        if(typeof address.phone == 'undefined' || address.phone == ""){
            message.error('请填写手机号码');
            return;
        }

        let data = {
            detailinfo: typeof address.detail == 'undefined' ? "" : address.detail,
            contactname: address.contactname,
            mobile: address.phone,
            postal_code: address.postcode,
            default: typeof address.checked == 'undefined' || address.checked == false ? 0 : 1
        };

        address.addressid.forEach(function(val,index){

            switch (index){
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

        this.setState({add_modal: false, loading: true});
        let url = getBaseUrl();

        if(typeof address.id !='undefined'){
            //编辑地址
            data.id = address.id;
            url += '/mengwu/mengwuApi/address_update';
        }else{
            //添加新地址
            url += '/mengwu/mengwuApi/address_add';
        }

        _post(url,jQuery.param(data),{data_back: this.save_response});
    },
    save_response(data){
        if(data.status){
            this.update();
        }else{
            this.jump(data);
            message.error('保存失败');
            this.setState({loading: false});
        }
    },
    add_modal_cancel(){
        this.setState({add_modal: false});
    },
    edit_address(data){
        if(typeof data.id == "undefined"){
            message.error("地址数据异常,无法编辑，请刷新后重试！");
            return;
        }
        if(data.default==1) data.checked = true;
        this.setState({address: data, add_modal: true});
    },
    selectAddress(id){
        let c = false;
        let data = this.state.data.map(function(val){
            if(val.id == id){
                if(!val.selected) c = true;
                val.selected = true;
            }else{
                val.selected = false;
            }
            return val;
        });
        if(c){
            this.setState({data: data});
            this.calcFreightFee();
        }
    },
    calcFreightFee(){
        //计算运费
        let from = order_info.from ? order_info.from : 'unknown';
        if(from=='unknown'){
            console.log('运费计算失败');
            return;
        }
        this.setState({freight_cost: '计算中...'});
        let url = getBaseUrl() + '/mengwu/mengwuApi/order_freight_cost';
        let address_id = 0;
        let cart_ids = [];
        this.state.data.forEach(function(val){
            if(val.selected) address_id = val.id;
        });
        if(address_id ==0){
            console.log('请选择地址');
        }
        cart_ids = order_items.map(function(val){
            return val.id;
        });

        let data = {
            from: from,
            address_id: address_id
        };
        if(from == 'spcart'){
            data.cart_ids = cart_ids.join(',');
        }
        if(from == 'buy'){
            if(!order_info.order_items || !order_info.order_items[0]){
                console.log('运费计算失败,商品信息不存在');
                this.setState({freight_cost: 0});
                return;
            }
            data.pid = order_info.order_items[0].p_id;
            data.count = order_info.order_items[0].count;
        }

        _post(url,jQuery.param(data),{data_back: this.calcFreightFee_res});

    },
    calcFreightFee_res(data){
        if(data.status){
            this.setState({freight_cost: parseFloat(data.info)});
        }else{
            this.jump(data);
        }
    },
    onChangeUseBalance(event){
        this.setState({use_balance: event.target.checked})
    },
    onChangePayType(type){
        //支付方式改变
        this.setState({other_pay_type: type});
    },
    confirmAndPay(){
        //生成订单
        let url = getBaseUrl() + '/mengwu/order/order_add';
        let note = this.state.note;
        let address_id = 0;
        let cart_ids = [];
        let youhui = this.state.youhui;
        let money = 0;
        let balance = this.state.balance > 0 ? this.state.balance : 0;
        let total_price = this.calcTotalPrice();
        if(isNaN(total_price)){
            message.error('请稍等~~');
            return;
        }
        cart_ids = order_items.map(function(val){
            return val.id;
        });
        this.state.data.forEach(function(val){
            if(val.selected) address_id = val.id;
        });
        if(address_id ==0){
            message.error('请选择收货地址！');
            return;
        }

        this.setState({loading: true});

        if(this.state.use_balance){
            if(balance >= total_price){
                money = total_price;
            }else{
                money = balance;
            }
        }

        let data = {
            note: note,
            address_id : address_id,
            coupon_id: youhui.coupon_id,
            red_id: youhui.redEnvelope_id,
            data: JSON.stringify(order_info),
            money: money
        };
        
        _post(url,jQuery.param(data),{data_back: this.orderAdd_res});
        
    },
    orderAdd_res(data){
        if(data.status){
            if(data.info.total_price>0){
                //打开新窗口支付
                this.setState({loading: false, pay_confirm_modal: true,pay_info: data.info});
                console.log('打开新窗口支付');
                let form = $('<form></form>');
                let action = getBaseUrl() + '/mengwu/order/jump2Pay';
                form.attr({action:action,method:'post',target:'_blank'});
                let pay_info_input = $('<input type="text" name="pay_info" />');
                pay_info_input.attr('value',JSON.stringify(data.info));
                let type_input = $('<input type="text" name="type" />');
                type_input.attr('value',this.other_pay_type);
                form.append(pay_info_input,type_input);
                $('body').append(form);
                form.submit().remove();
            }else{
                this.jump_order_detail();
            }
            
        }else{
            this.jump(data);
            message.error(data.info);
            this.setState({loading: false});
        }
    },
    confirmPay(){
        let pay_info = this.state.pay_info;
        let form = $('<form></form>');
        let action = getBaseUrl() + '/mengwu/order/jump2Pay';
        form.attr({action:action,method:'post',target:'_blank'});
        let pay_info_input = $('<input type="text" name="pay_info" />');
        pay_info_input.attr('value',JSON.stringify(pay_info));
        let type_input = $('<input type="text" name="type" />');
        type_input.attr('value',this.other_pay_type);
        form.append(pay_info_input,type_input);
        $('body').append(form);
        form.submit().remove();
    },
    jump_order_detail(){
        window.location.href = getBaseUrl() + '/mengwu/user/order';
    },

    render(){
        let address_url = getBaseUrl() + '/mengwu/user/address';
        let add = Add_address.bind(this)();
        let address_list = Address.bind(this)();
        let pay_type = Pay_type.bind(this)();
        let items = Order_Items.bind(this)();
        let total_detail = Total_detail.bind(this)();
        let selected_addr = {};
        let total_price = this.calcTotalPrice();
        let balance = this.state.balance; //余额
        this.state.data.forEach(function(val){
            if(val.selected) selected_addr = val;
        });
        const pay_type_desc = ['支付宝','微信','财付通'];
        let pay_desc = '';
        if(this.state.use_balance && total_price<=balance){
            pay_desc = '使用余额支付';
        }else{
            pay_desc = "使用"+pay_type_desc[this.state.other_pay_type]+'支付';
        }

        return(
            <Spin spinning={this.state.loading}>
                {add}
                <div className="top">
                    <h1>确认订单</h1>
                    <div className="progress"></div>
                </div>
                <div className="confirm-inner">
                    <div className="step">
                        <h4>选择收货地址</h4> <a href={address_url} target="_blank">管理收货地址</a>
                    </div>
                    {address_list}
                    <div className="step nob">
                        <h4>确认订单信息</h4>
                    </div>
                    {items}
                    {total_detail}
                    <div className="step">
                        <h4>选择支付方式</h4>
                    </div>
                    {pay_type}
                    <div className="confirm-info-wrap">
                        <div className="confirm-info">
                            <p>{pay_desc}<em>{total_price}</em>元</p>
                            <p>寄送至：<span>{selected_addr.address} {selected_addr.detail}</span></p>
                            <p>收货人：<span>{selected_addr.contactname} {selected_addr.phone}</span></p>
                        </div>
                    </div>
                    <Modal title="确认支付" visible={this.state.pay_confirm_modal} okText="已支付" cancelText="前往支付" closable={false} style={{top: 300}} onOk={this.jump_order_detail} onCancel={this.confirmPay}>
                        <p>请在新打开的页面中完成支付</p>
                        <p>1.如果没有跳出新窗口，请检查浏览器是否允许弹出新窗口</p>
                        <p>2.点击前往支付可重新支付</p>
                    </Modal>
                    <div className="oper-btn clear text-right">
                        <a href="javascript:void(0);" className="btn btn-confirm" onClick={this.confirmAndPay}>确认并支付</a>
                    </div>
                </div>
            </Spin>
        );
    }
    
});

ReactDOM.render(
    <Confirm />,
    document.getElementById('order-confirm')
);