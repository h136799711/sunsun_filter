const { Spin,message,Popconfirm } = antd;

const Num_selector = React.createClass({
    getInitialState(){
      return {quantity: this.props.quantity};
    },
    handleChange: function(event) {
        let q = event.target.value;
        if(!isNaN(q)) this.change(q);
    },
    minus(){
        let q = this.state.quantity;
        if(q>1){
            q --;
        }else{
            q = 1;
        }
        this.change(q);
    },
    plus(){
        let q = this.state.quantity;
        q++;
        this.change(q);
    },
    blur(){
        let q = this.state.quantity;
        if(q<1) q = 1;
        q = parseInt(q);
        this.change(q);

    },
    change(q){
        if(typeof(this.props.max)!='undefined'){
            if(!isNaN(max) && max!='' && q >= this.props.max){
                q = this.props.max;
            }
        }
        this.setState({quantity: q});
        this.props.changeQuantity(q);
    },
    render(){
        return(
            <div className="num-selector">
                <div className="op m" onClick={this.minus}>-</div>
                <input type="text" value={this.state.quantity} onChange={this.handleChange} onBlur={this.blur} />
                <div className="op p" onClick={this.plus}>+</div>
            </div>
        );
    }
});

const Spcart_list = React.createClass({
    getInitialState(){
        return {price_all: this.props.spcart.price * 100 * this.props.spcart.count/100};
    },
    calc_price_all(quantity){
        let price_all = this.props.spcart.price * 100 * quantity / 100;
        this.setState({price_all: price_all});
        this.props.update_counts(this.props.spcart.id,quantity);
    },
    delete(){
      this.props.delete_list(this.props.spcart.id);
    },
    check_Onchange(event){
        this.props.check_item(this.props.spcart.id,event.target.checked);
    },
    render(){
        let sp_url = getBaseUrl() + '/mengwu/spdetail/index/id/' + this.props.spcart.p_id;
        return(
            <div className="list">
                <div className="list-wrap">
                    <div className="column info">
                        <div className="column chose"><label><input type="checkbox" checked={this.props.spcart.checked?"checked":""} onChange={this.check_Onchange} /></label></div>
                        <div className="sp-img"><a href={sp_url} target="_blank"><img src={this.props.spcart.icon_url} /></a></div>
                        <div className="introduce">
                            <div className="caption">
                                <a href={sp_url} target="_blank">{this.props.spcart.name}</a>
                            </div>
                            <div className="sorted">
                                <span>{this.props.spcart.sku_desc}</span>
                            </div>
                        </div>
                    </div>
                    <div className="column price text-center">{this.props.spcart.price}</div>
                    <div className="column quantity text-center">
                        <Num_selector quantity={this.props.spcart.count} changeQuantity={this.calc_price_all} />
                    </div>
                    <div className="column price-all text-center">{this.state.price_all}</div>
                    <div className="column oper text-center">
                        <Popconfirm title="确定要从购物车中删除这件商品吗？" onConfirm={this.delete} >
                            <a href="javascript:void(0);">删除</a>
                        </Popconfirm>
                    </div>
                </div>
            </div>
        );
    }
});

const Spcart_head = React.createClass({
    check_Onchange(event){
        this.props.check_all_item(event.target.checked);
    },
    render(){
        return(
            <div className="head">
                <div className="column info">
                    <div className="column chose-all"><label><input type="checkbox" checked={this.props.checked_all?"checked":"" } onChange={this.check_Onchange} />全选</label></div>商品信息
                </div>
                <div className="column price text-center">单价（元）</div>
                <div className="column quantity text-center">数量</div>
                <div className="column price-all text-center">小计（元）</div>
                <div className="column oper text-center">操作</div>
            </div>
        );
    }
});

const Spcart_sure = React.createClass({
    check_Onchange(event){
        this.props.check_all_item(event.target.checked);
    },
    delete(){
        let spcart = this.props.spcart;
        let id = [];
        spcart.map(function(val){
            if(val.checked===true){
                id.push(val.id);
            }
        });
        if(id.length!=0){
            this.props.delete_checked(id);
        }else{
            message.error('没有选中任何商品！');
        }

    },
    confirm(){
        let spcart = this.props.spcart;
        let ids = [];
        let counts = [];
        spcart.forEach(function(val){
            if(val.checked){
                ids.push(val.id);
                counts.push(parseInt(val.count));
            }
        });
        if(ids.length == 0){
            message.error('请先选择需要结算的商品！');
            return false;
        }else{
            this.props.loading(true);
            message.loading('加载中...',0);
            //修改购物车数量
            let url = getBaseUrl() + '/mengwu/mengwuApi/spcart_edit';
            let data = {
                id: ids.join(','),
                count: counts.join(',')
            };

            _post(url,jQuery.param(data),{data_back: this.edit_response});

        }
    },
    edit_response(data){
        if(data.status){

            let ids = [];
            this.props.spcart.forEach(function(val){
                if(val.checked){
                    ids.push(val.id);
                }
            });

            this.props.loading(false);

            let form = $('<form></form>');
            let action = getBaseUrl() + '/mengwu/order/confirm';
            form.attr({action:action,method:'post',target:'_self'});
            let ids_input = $('<input type="text" name="cart_ids" />');
            let from_input = $('<input type="text" name="from" value="spcart" />');
            ids_input.attr('value',ids);
            form.append(ids_input,from_input);
            $('body').append(form);
            form.submit().remove();

        }else{
            if(typeof data.info.redirect !="undefined"){
                window.location.href = data.info.redirect;
            }else{
                message.error(data.info);
                this.props.loading(false);
            }

        }
    },
    render(){
        let total_num = 0;
        let total_price = 0;
        this.props.spcart.forEach(function(n){
            if(n.checked){
                total_num += parseInt(n.count);
                total_price += parseInt(n.count) *100 * n.price;
            }
        });
        total_price = total_price / 100;

        return(
            <div className="sure">
                <div className="column chose-all"><label><input type="checkbox" checked={this.props.checked_all?"checked":""} onChange={this.check_Onchange} />全选</label></div>
                <div className="column delete">
                    <Popconfirm title="确定要从购物车中删除选中的商品吗？" onConfirm={this.delete} >
                        <a href="javascript:void(0);">删除选中</a>
                    </Popconfirm>
                </div>
                <div className="column total fr">
                    共有{total_num}件商品，已优惠<span className="dis">0.00</span>元，总计（不含运费）：<span className="total-price"><em className="rmb">￥</em>{total_price}</span>
                    <a className="btn confirm" href="javascript:void(0);" onClick={this.confirm}>确认订单</a>
                </div>
            </div>
        );
    }
});

const Spcart = React.createClass({
    getDefaultProps(){
        return({data:[]});
    },
    getInitialState(){
        return({spcart: this.props.data, loading: true, todel: []});
    },
    componentDidMount(){
        let url = getBaseUrl() + '/mengwu/mengwuApi/spcart_query';
        let data = {};
        _post(url,jQuery.param(data),{data_back: this.init_response});

    },
    init_response(data){
        if(data.status){
            this.setState({spcart: data.info.carts ,loading: false});
        }else{
            if(typeof data.info.redirect !="undefined"){
                window.location.href = data.info.redirect;
            }
        }
    },
    update_counts(id,q){
        let spcart = this.state.spcart.map(function(val) {
            if(val.id==id) val.count = q;
            return val;
        });
        this.setState({spcart: spcart});

    },
    delete_list(id){
        this.setState({loading: true, todel: id});

        let ids = "";

        if(Array.isArray(id)){
            ids = id.join(",")
        }else{
            ids = id;
        }

        let url = getBaseUrl() + '/mengwu/mengwuApi/spcart_delete';
        let data = {
            id: ids
        };

        _post(url,jQuery.param(data),{data_back: this.delete_response});

    },
    delete_response(data){
        if(data.status){
            //如果成功，删除记录
            let spcart = this.state.spcart;
            let todel = this.state.todel;
            let new_spcart = [];
            spcart.forEach(function(val){
                let s = true;
                if(Array.isArray(todel)){
                    for(let i=0; i<todel.length; i++){
                        if(val.id==todel[i]) s = false;
                    }
                }else{
                    if(val.id==todel) s = false;
                }
                if(s) new_spcart.push(val);
            });
            message.success('删除成功');
            this.setState({spcart: new_spcart, loading: false});
        }else{
            if(typeof data.info.redirect !="undefined"){
                message.error(data.info.info);
                setTimeout(function(){
                    window.location.href = data.info.redirect;
                },1000);
            }else{
                message.error('删除失败');
            }
        }
        this.setState({todel: []});
    },
    check_item(id,checked){
        let spcart = this.state.spcart.map(function(val){
            if(val.id==id) val.checked = checked;
            return val;
        });
        this.setState({spcart: spcart});
    },
    check_all_item(checked){
        let spcart = this.state.spcart.map(function(val){
            val.checked = checked;
            return val;
        });
        this.setState({spcart: spcart});
    },
    loading(s){
        this.setState({loading: s});
    },
    render(){
        let checked_all = true;
        let spcart_lists = this.state.spcart.map(function(val){
            if(!val.checked) checked_all = false;
            return <Spcart_list spcart={val} update_counts={this.update_counts} delete_list={this.delete_list} check_item={this.check_item} />;
        }.bind(this));
        if(spcart_lists.length==0) checked_all = false;

        return(
            <Spin spinning={this.state.loading}>
                <div className="spcart-wrap">
                    <Spcart_head checked_all={checked_all} check_all_item={this.check_all_item} />
                    {spcart_lists}
                    <Spcart_sure loading={this.loading} spcart={this.state.spcart} checked_all={checked_all} check_all_item={this.check_all_item} delete_checked={this.delete_list} />
                </div>
            </Spin>
        );
    }
});

const data = {
    list:[
        {id:1,uid:1,p_id:1,sku_desc:"颜色：大红 尺寸：90",icon_url:"http://ww2.sinaimg.cn/crop.0.0.1080.1080.1024/005CfA2Tjw8ep0z730gxcj30u00u040l.jpg"
            ,price:259,count:2,name:"SIVENFY高支欧美田园床单纯棉四件套1.5米-1.8米宽床通用 清香怡人XXXX"},
        {id:2,uid:1,p_id:1,sku_desc:"颜色：大绿 尺寸：190",icon_url:"http://ww2.sinaimg.cn/crop.0.0.1242.1242.1024/d2d7a9d8jw8etui148nmtj20yi0yiwgz.jpg"
            ,price:158,count:1,name:"SIVENFY高支欧美田园床单纯棉四件套2.5米-3.8米宽床通用 清香怡人XXXX"}
    ]
};



ReactDOM.render(
    //<Spcart data={data.list} />
    <Spcart />,
    document.getElementById('spcart')
);

