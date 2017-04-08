const { address_json: option } = mengwu;
const { Cascader,Input,Checkbox,message,Spin,Popconfirm } = antd;
const Aaddress_editor = React.createClass({
    addressOnChange(value, selectedOptions){
        let data = this.props.data;
        data.addressid = value;
        data.address = selectedOptions.map(function(val){
            return val.label;
        });
        this.props.onEdit(data);
    },
    detailOnChange(event){
        let data = this.props.data;
        data.detail = event.target.value;
        this.props.onEdit(data);
    },
    postcodeOnChange(event){
        let data = this.props.data;
        data.postcode = event.target.value;
        this.props.onEdit(data);
    },
    contactnameOnChange(event){
        let data = this.props.data;
        data.contactname = event.target.value.substr(0,25);
        this.props.onEdit(data);
    },
    phoneOnChange(event){
        if(!isNaN(event.target.value)){
            //手机号只能输入数字
            let data = this.props.data;
            data.phone = event.target.value;
            this.props.onEdit(data);
        }
    },
    checkOnChange(event){
        let data = this.props.data;
        data.checked = event.target.checked;
        this.props.onEdit(data);
    },
    render(){

        let data = this.props.data;
        let tip = "新增收货地址";
        if(typeof(data.id)!='undefined'){
            tip = "修改收货地址";
        }
        return(
            <div className="address-form">
                <div className="tip"><h4>{tip}</h4><span>均为必填项</span></div>
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
                <div className="form-group">
                    <div className="l"></div>
                    <div className="r">
                        <a className="btn save" href="javascript:void(0);" onClick={this.props.save} >保存</a>
                    </div>
                </div>
            </div>
        );
    }
});

const Address_list = React.createClass({
    onEdit(){
        let data = {};
        _.assign(data, this.props.data);
        if(this.props.data.default==1) data.checked = true;
        this.props.onEdit(data);
    },
    onDelete(){
        this.props.delete(this.props.data.id);
    },
    render(){
        let data = this.props.data;
        return(
            <tr className="list">
                <td className="contactname">{data.contactname}</td>
                <td className="address">{data.address}</td>
                <td className="detail">{data.detail}</td>
                <td className="postcode">{data.postcode}</td>
                <td className="phone">{data.phone}</td>
                <td className="oper" className={data.default==1?'default':""}>
                    <a href="javascript:void(0);" onClick={this.onEdit}>修改</a>|
                    <Popconfirm title="确定要删除这个地址吗？" onConfirm={this.onDelete} >
                        <a href="javascript:void(0);" >删除</a>
                    </Popconfirm>
                </td>
            </tr>
        );
    }
});

const address_list = [
    {id: 1, contactname: "艾米", address: "北京市市辖区东城区", addressid: ['110000','110100','110101'], detail: "万亚金沙湖1号2幢1515", postcode: "310018", phone: "18612345662"},
    {id: 2, contactname: "艾米", address: "北京市市辖区东城区", addressid: ['110000','110100','110101'], detail: "万亚金沙湖1号2幢1515", postcode: "310018", phone: "18612345662"}
];

const Address = React.createClass({
    getDefaultProps(){
        return({data: []});
    },
    getInitialState(){
        let data = this.props.data;
        return({address: {},data: data});
    },
    componentDidMount(){

        this.update();
    },
    onEdit(data){
        this.setState({address: data});
    },
    addAddress(){
        this.setState({address: {detail:""}});
        $('html, body').animate({
            scrollTop: 0
        }, 200);
    },
    update(){
        this.setState({loading: true});
        let url = getBaseUrl() + '/mengwu/mengwuApi/address_query';
        let data = {};
        _post(url,jQuery.param(data),{data_back: this.update_response});
    },
    update_response(data){
        this.setState({loading: false});
        if(data.status){
            this.setState({data: data.info});
        }else{
            this.jump(data);
        }
    },
    delete(id){
        this.setState({loading: true});
        let url = getBaseUrl() + '/mengwu/mengwuApi/address_delete';
        let data = {
            id: id
        };
        _post(url,jQuery.param(data),{data_back: this.delete_response});
    },
    delete_response(data){
        if(data.status){
            this.update();
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
    save(){
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

        this.setState({loading: true});
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
            message.success('保存成功');
            this.update();
        }else{
            this.jump(data);
            message.error('保存失败');
            this.setState({loading: false});
        }
    },
    render(){
        let list = this.state.data.map(function(val){
            return <Address_list data={val} onEdit={this.onEdit} delete={this.delete} />;
        }.bind(this));
        if(list.length==0) list = <tr className="list"><td colSpan="6">暂无收货地址</td></tr>;
        return(
            <div>
                <Spin spinning={this.state.loading}>
                    <Aaddress_editor data={this.state.address} onEdit={this.onEdit} save={this.save} />
                    <div className="address-list">
                        <div className="tip"><h4>已保存的有效地址</h4><a href="javascript:void(0);" onClick={this.addAddress}>添加新地址</a></div>
                        <table width="100%">
                            <thead>
                            <tr className="head">
                                <th className="contactname">收货人</th>
                                <th className="address">所在地区</th>
                                <th className="detail">详细地址</th>
                                <th className="postcode">邮编</th>
                                <th className="phone">手机</th>
                                <th className="oper">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {list}
                            </tbody>
                        </table>
                    </div>
                </Spin>

            </div>
        );
    }
});

ReactDOM.render(
    <Address />,
    document.getElementById('address')
);