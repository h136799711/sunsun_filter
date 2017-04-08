const { message } = antd;

const Thumb_list = React.createClass({
    onMouseOver(){
        this.props.setThumbSelected(this.props.item_pic.id);
    },
    render(){
        return(
            <li data-index={this.props.item_pic.id} className={this.props.item_pic.selected?'select':''}>
                <div className="pic s50" onMouseOver={this.onMouseOver}><img src={this.props.item_pic.s50} /></div>
            </li>
        );
    }
});

const Main_pic = React.createClass({
    getInitialState(){

        let zoom_icon = "zoom-icon icon iconfont";

        return({zoom_icon: zoom_icon});
    },
    onMouseMove(event){
        let wrap_rect = this.refs.imagezoom_wrap.getBoundingClientRect();
        let img_rect = this.refs.img.getBoundingClientRect();

        let zoom_lens = this.props.zoom_lens;

        if(event.clientX > img_rect.left && event.clientX < img_rect.right && event.clientY > img_rect.top && event.clientY < img_rect.bottom){
            zoom_lens.display = 'block';
            zoom_lens.left = event.clientX - zoom_lens.width/2 - wrap_rect.left;
            zoom_lens.top = event.clientY -zoom_lens.height/2 - wrap_rect.top;
            zoom_lens.left = zoom_lens.left < img_rect.left - wrap_rect.left ?img_rect.left - wrap_rect.left:zoom_lens.left;
            zoom_lens.left = zoom_lens.left > img_rect.width - zoom_lens.width + img_rect.left - wrap_rect.left?img_rect.width - zoom_lens.width + img_rect.left - wrap_rect.left:zoom_lens.left;

            zoom_lens.top = zoom_lens.top < img_rect.top - wrap_rect.top ?img_rect.top - wrap_rect.top:zoom_lens.top;
            zoom_lens.top = zoom_lens.top > img_rect.height - zoom_lens.height + img_rect.top - wrap_rect.top?img_rect.height - zoom_lens.height + img_rect.top - wrap_rect.top:zoom_lens.top;

            zoom_lens.offsetLeft = zoom_lens.left - (img_rect.left - wrap_rect.left);
            zoom_lens.offsetTop = zoom_lens.top - (img_rect.top - wrap_rect.top);
            zoom_lens.img_rect = img_rect;

        }

        this.props.setZoomLens(zoom_lens);

        let zoom_icon = "zoom-icon icon iconfont hide";
        this.setState({zoom_icon: zoom_icon});
    },
    OnMouseOut(){
        let zoom_lens = this.props.zoom_lens;
        zoom_lens.display = 'none';
        this.props.setZoomLens(zoom_lens);

        let zoom_icon = "zoom-icon icon iconfont";

        this.setState({zoom_lens: zoom_lens, zoom_icon: zoom_icon});
    },
    render(){
        return(
            <div className="main-pic">
                <a href="javascript:void(0);" ref="imagezoom_wrap">
                    <div className="imagezoom-wrap" onMouseMove={this.onMouseMove} onMouseOut={this.OnMouseOut} >
                        <img src={this.props.pic} ref="img" />
                        <span className="imagezoom-lens" style={this.props.zoom_lens}></span>
                    </div>
                </a>
                <div className={this.state.zoom_icon}>&#xe690;</div>
            </div>
        );
    }
});

const Item_gallery = React.createClass({
    getInitialState(){
        let gallery = this.props.data;
        gallery[0].selected = true;
        let zoom_lens = {
            position: 'absolute',
            display: 'none',
            width: 400/950*400,
            height: 400/950*400,
            top: 0,
            left:0,
            img_rect: {
                width: 400,
                height: 400
            },
            original_rect: {
                width: 950,
                height :950
            },
            offsetLeft: 0,
            offsetTop: 0

        };

        return ({gallery: gallery,main_pic: {s400:gallery[0].s400, original:gallery[0].original}, zoom_lens: zoom_lens});
    },
    setZoomLens(p){

        let original = this.refs.zoom_img.getBoundingClientRect();

        let zoom_lens = this.state.zoom_lens;

        let w = 400*400/original.width;
        let h = 400*400/original.height;

        zoom_lens.width = w < h ?w:h;
        zoom_lens.height = w < h ?w:h;

        p.original_rect = original;

        this.setState({zoom_lens: p});
    },

    setThumbSelected(id){
        let main_pic  = "";
        let gallery = this.state.gallery.map(function(val){
            if(val.id == id){
                main_pic = {
                    s400    : val.s400,
                    original: val.original
                }
                val.selected = true;
            }else{
                val.selected = false;
            }
            return val;
        });

        this.setState({gallery: gallery, main_pic: main_pic});
    },
    render(){
        let thumb_list = this.state.gallery.map(function(val){
            return <Thumb_list item_pic={val} setThumbSelected={this.setThumbSelected} />;
        }.bind(this));

        let zoom_lens = this.state.zoom_lens;

        let img_pos = {
            left: -zoom_lens.offsetLeft * zoom_lens.original_rect.width / zoom_lens.img_rect.width,
            top: -zoom_lens.offsetTop * zoom_lens.original_rect.height / zoom_lens.img_rect.height
        };

        return(
            <div className="item-gallery">
                <Main_pic pic={this.state.main_pic.s400} setZoomLens={this.setZoomLens} zoom_lens={this.state.zoom_lens}/>
                <div className={'zoom'} style={{display: this.state.zoom_lens.display}}>
                    <img ref="zoom_img" style={img_pos} src={this.state.main_pic.original} />
                </div>
                <ul className="thumb">
                    {thumb_list}
                </ul>
            </div>
        );
    }
});

// const gallery_list = [
//     {id: 1, s50: "//gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg_50x50.jpg", s400: "https://gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg_400x400.jpg_.webp", original:"https://gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg"},
//     {id: 2, s50: "//gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg_50x50.jpg", s400: "https://gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg_400x400.jpg_.webp", original:"https://gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg"}
// ];


ReactDOM.render(
    <Item_gallery data={sp_detail.gallery_list} />,
    document.getElementById('item_gallery')
);

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
            let max = this.props.max;
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


const Sku_prop = React.createClass({
    onClick(){
        let sku = this.props.sku;
        this.props.sku_click(sku.id, sku.vid);
    },
    render(){
        let sku = this.props.sku;
        return(
            <li className={this.props.selected?'selected':''} onClick={this.onClick}><a href="javascript:void(0);"><span>{sku.name}</span></a></li>
        );
    }
});

const Detail_wrap = React.createClass({
    getInitialState(){
        if(typeof this.props.data == 'undefined'){
            return({});
        }
        let state = {sku:{},stock:"",quantity:1, psku_id: 0};
        let detail = this.props.data;
        if(detail.has_sku==1){
            state.sku = detail.sku_id.map(function(v){
                return {id: v.id, vid: 0};
            });
        }else{
            state.stock = detail.sku_info.quantity;
            state.psku_id = detail.sku_info.id;
        }
        return({state: state, adding: false, buying: false, is_fav:  parseInt(detail.is_fav) ? true : false, loading:false});
    },
    sku_click(id,vid){
        let state = this.state.state;
        state.sku = state.sku.map(function(v){
            if(v.id==id) {
                if(v.vid == vid){
                    v.vid = 0; //取消选中
                }else{
                    v.vid = vid; //选中
                }
            }
            return v;
        });

        let sku_detail = this.calc_detail();
        state.stock = sku_detail.stock;
        state.psku_id = sku_detail.psku_id;

        this.setState({state: state});
    },
    calc_detail(){
        let detail = this.props.data;
        let price = "";
        let ori_price = "";
        let ifselected = true;
        let state = this.state.state;
        let sku_id = "";
        let stock = "";
        let psku_id = 0;
        let type = "";
        state.sku.forEach(function(v){
            if(v.vid!=0){
                sku_id += v.id + ':' + v.vid + ';' ;
            }else{
                ifselected = false;
            }
        });

        //判断分组,获取分组
        let group_info = null;
        if(detail.gid!=''){
            let g = detail.group_info ? detail.group_info : [];
            group_info = _.find(g,['group',detail.gid]);
        }


        if(!ifselected){
            //计算未选中时价格
            let min_price = 0;
            let max_price = 0;
            detail.sku_list.forEach(function(val){
                if(min_price == 0){
                    min_price = parseFloat(val.price);
                }
                else if(parseFloat(val.price) > min_price){
                    max_price = parseFloat(val.price);
                }
            });

            if(min_price == max_price || max_price == 0){
                price = min_price.toFixed(2);
            }else if(max_price!=0){
                price = min_price.toFixed(2) + '~' + max_price.toFixed(2);
            }
        }else{
            //计算选中时价格

            detail.sku_list.forEach(function(v){
                if(v.sku_id == sku_id){
                    //有分组则采用分组价
                    let gs = null; //分组sku信息
                    if(group_info){
                        if(group_info.sku_info){
                            gs = _.find(group_info.sku_info,['sku_id',sku_id]);
                        }
                    }
                    if(gs){
                        if(gs.price>0){
                            price = gs.price;
                            type = "优惠价:";
                        }else{
                            //分组价格为0时采用普通价格 或会员价格
                            if(parseFloat(v.member_price)>0){
                                type = "会员价:";
                                price = parseFloat(v.member_price);
                            }else{
                                price = v.price;
                            }

                        }
                    }else{
                        //不使用分组时采用普通价格 或会员价格
                        if(parseFloat(v.member_price)>0){
                            type = "会员价:";
                            price = v.member_price;
                        }else{
                            price = v.price;
                        }
                    }
                    ori_price = v.ori_price;
                    stock = v.quantity;
                    psku_id = v.id;
                }
            });

        }

        return {price:{price: price, ori_price: ori_price,type:type}, stock: stock, psku_id: psku_id};

    },
    changeQuantity(q){
        let state = this.state.state;
        state.quantity = q;
        this.setState({state: state, tip:""});
    },
    selectOk(){
        //判断可否执行加入购物车和购买操作
        let state = this.state.state;
        let detail = this.props.data;
        let psku_id = state.psku_id;
        let tip = "";
        let add = true;
        if(detail.has_sku == 1){
            let sku = state.sku;
            if(Array.isArray(sku)){
                sku.forEach(function(val){
                    if(val.vid==0){
                        add = false;
                        tip = "请先选择规格!";
                    }
                });
                if(psku_id>0){
                    let sku_info = _.find(detail.sku_list,{id:psku_id});
                    if(sku_info.quantity<=0){
                        add = false;
                        tip = "商品该规格库存不足!";
                    }
                }
            }
            else{
                add = false;
                tip = "规格出错，无法加入购物车!";
            }
        }else{
            let sku_info = detail.sku_info;
            if(sku_info.quantity<=0){
                add = false;
                tip = "商品该规格库存不足!";
            }
        }

        if(!add){
            this.setState({tip: tip});
            return false;
        }else{
            return true;
        }
    },
    buy(){
        //立即购买
        let state = this.state.state;
        let detail = this.props.data;
        if(!this.selectOk()) return;

        let buying = message.loading('加载中...',0);

        this.setState({buying});//禁止立即购买按钮

        let form = $('<form></form>');
        let action = getBaseUrl() + '/mengwu/order/confirm';
        form.attr({action:action,method:'post',target:'_self'});
        let pid_input = $('<input type="text" name="pid" />');
        let count_input = $('<input type="text" name="count" />');
        let psku_id_input = $('<input type="text" name="psku_id" />');
        let from_input = $('<input type="text" name="from" value="buy" />');
        pid_input.attr('value',detail.id);
        count_input.attr('value',state.quantity);
        psku_id_input.attr('value',state.psku_id);
        form.append(pid_input,count_input,psku_id_input,from_input);
        $('body').append(form);
        form.submit().remove();
    },
    add2cart(){
        //加入购物车
        let state = this.state.state;
        let detail = this.props.data;
        if(!this.selectOk()) return;

        this.setState({adding: true});//禁止加入购物车按钮
        let url = getBaseUrl() + '/mengwu/mengwuApi/spcart_add';
        let data = {
            pid: detail.id,
            count: state.quantity,
            psku_id: state.psku_id,
            group_id: detail.gid
        };

        _post(url,jQuery.param(data),{data_back: this.addsp_response});
    },
    addsp_response(data){
        if(data.status){
            message.success('添加成功!');
            this.setState({adding: false});//取消禁止加入购物车按钮
            setTimeout(function(){
                window.location.reload();
            },1000);
        }else{
            if(typeof data.info.redirect !="undefined"){
                message.error(data.info.info);
                setTimeout(function(){
                    window.location.href = data.info.redirect;
                },1000);
            }else{
                message.error(data.info);
                this.setState({adding: false});//取消禁止加入购物车按钮
            }
        }
    },
    sp_fav(){
        //商品收藏

        if(this.state.loading) return;

        let url = getBaseUrl() + '/mengwu/mengwuApi/product_favorites';
        let is_fav = this.state.is_fav;
        let data = {
            pid: this.props.data.id,
            value: is_fav ? 0 : 1
        };

        this.setState({loading: true});
        _post(url,jQuery.param(data),{data_back: function(data){
            this.setState({loading: false});
            if(data.status){
                this.setState({is_fav: !is_fav});
                message.success(data.info);
            }else{
                if(typeof data.info.redirect !="undefined"){
                    message.error(data.info.info);
                    setTimeout(function(){
                        window.location.href = data.info.redirect;
                    },1000);
                }
            }

        }.bind(this)});
    },
    render(){
        if(typeof this.props.data == 'undefined'){
            return(
                <div className="detail-wrap">
                    <div className="title">
                        <h3 className="main-title">商品数据错误</h3>
                    </div>
                </div>

            );
        }
        let detail = this.props.data;
        let price = {};
        let sku_lists = [];
        let sku_detail = {};
        let quantity = this.state.state.quantity;
        let tip = <p>{this.state.tip}</p>;

        if(detail.has_sku==1){
            //有多规格

            sku_detail = this.calc_detail();

            price = sku_detail.price;

            sku_lists = detail.sku_name.map(function(v,n){

                let sku_id = detail.sku_id[n];
                let sku = v.vid.map(function(x,n){
                    let selected = false;
                    let id = sku_id.id, vid = sku_id.vid[n];
                    this.state.state.sku.forEach(function(v){
                        if(v.id==id && v.vid==vid) selected = true;
                    });

                    return <Sku_prop sku={{id: id, vid: vid, name: x}} sku_click={this.sku_click} selected={selected} />;
                }.bind(this));
                return (
                    <dl className="proptype clear">
                        <dt>{v.id}</dt>
                        <dd>
                            <ul >
                                {sku}
                            </ul>
                        </dd>
                    </dl>
                );
            }.bind(this));

        }
        else{
            //没有多规格

            //判断分组,获取分组
            let group_info = null;
            if(detail.gid!=''){
                let g = detail.group_info ? detail.group_info : [];
                group_info = _.find(g,['group',detail.gid]);
            }

            //有分组则采用分组价
            let gs = null; //分组sku信息
            let sku_info = detail.sku_info;
            let sku_id = sku_info.id;
            if(group_info){
                if(group_info.sku_info){
                    gs = _.find(group_info.sku_info,['sku_id',sku_id]);
                }
            }
            if(gs){
                if(gs.price>0){
                    price.price = gs.price;
                    price.type = "优惠价:";
                }else{
                    //分组价格为0时采用普通价格 或会员价格
                    if(sku_info.member_price!=""){
                        price.type = "会员价:";
                        price.price = sku_info.member_price;
                    }else{
                        price.price = sku_info.price;
                    }

                }
            }else{
                //不使用分组时采用普通价格 或会员价格
                if(parseFloat(sku_info.member_price)>0){
                    price.type = "会员价:";
                    price.price = parseFloat(sku_info.member_price);
                }else{
                    price.price = sku_info.price;
                }
            }

            price.ori_price = sku_info.ori_price;
            sku_detail.stock = sku_info.quantity;
        }
        if(sku_detail.stock!='' && sku_detail.stock < quantity){
            tip = <p>您所填写的宝贝数量超过库存！</p>;
        }
        if(sku_detail.stock!='' && sku_detail.stock < quantity){
            tip = <p>您所填写的宝贝数量超过库存！</p>;
        }

        let buy_limit = parseInt(detail.buy_limit);

        let buylimit_prop = function(){
            if(buy_limit>0){
                if(buy_limit < quantity) tip = <p>该商品限购{buy_limit}件！</p>;
                return(
                    <div className="promo">
                        <dl className="proptype clear">
                            <dt>限购:</dt>
                            <dd>{parseInt(buy_limit)}件</dd>
                        </dl>
                    </div>
                );
            }
        }();


        // 获取服务信息
        let baoyou = false; //是否包邮
        let max = buy_limit >0 ? buy_limit :this.state.state.stock;
        let service_hash = !detail.service_info ? null : {
            6102: '包邮',
            6103: '24小时发货',
            6104: '退货补运费',
            6105: '假就赔',
            6106: '贵就赔',
            6107: '慢就赔'
        };

        let service_list = [];
        if(service_hash){
            detail.service_info.forEach(function(val){
                if(service_hash[val]){
                    if(service_hash[val]=='包邮') baoyou = true;
                    service_list.push(service_hash[val]);
                }
            });
        }

        let service = service_list.length==0 ? null : [
            <div className="service">
                <dl className="proptype clear">
                    <dt>服务:</dt>
                    <dd>
                        {
                            service_list.map(function(val){
                                return <span className="service-item">{val}</span>
                            })
                        }
                    </dd>
                </dl>
            </div>
        ];
        //店铺优惠信息
        let store_benefit = [];
        if(detail.store_benefit){
            const s = detail.store_benefit;
            const time = new Date().getTime();
            if(s.start_time*1000 <= time && time <=s.end_time*1000){
                if(s.discount_money>0){
                    store_benefit.push(
                        <div className="promos"><span className="detail-icon reduce">减</span>&nbsp;{`满${s.condition}元减${s.discount_money}`}</div>
                    );
                }
                if(s.free_shipping){
                    store_benefit.push(
                        <div className="promos"><span className="detail-icon nonPostfee">包邮</span>&nbsp;{`满${s.condition}元包邮`}</div>
                    );
                }
            }
        }
        let promo = store_benefit.length==0 ? null : [
            <div className="promo">
                <dl className="proptype clear">
                    <dt>优惠:</dt>
                    <dd>{store_benefit}</dd>
                </dl>
            </div>
        ];
        return(
            <div className="detail-wrap">
                <div className="title">
                    <h3 className="main-title">{detail.name}</h3>
                    <p className="subtitle">{detail.synopsis}</p>
                </div>
                <div className="meta">
                    <span className="PromoPrice">{price.type}<em className="rmb">￥</em>{price.price}</span>
                    {baoyou ? <span>&nbsp;<span className="detail-icon nonPostfee">包邮</span>&nbsp;</span> : null}
                    {price.ori_price!=''?<span className="OriPrice">原价:<del><em className="rmb">￥</em>{price.ori_price}</del></span>:''}
                </div>
                {
                    /*<div className="Postfee">
                        <dl className="proptype clear">
                            <dt>运费:</dt>
                            <dd>包邮（ 偏远地区除外 ）</dd>
                        </dl>
                    </div>*/
                }
                {promo}
                {buylimit_prop}
                <div className="skin">
                    {sku_lists}
                    <dl className="proptype clear">
                        <dt>数量:</dt>
                        <dd>
                            <Num_selector quantity={1} changeQuantity={this.changeQuantity} max={max} />
                            <span className="stock">件 {sku_detail.stock!=''?'(库存' + sku_detail.stock + '件)':''}</span>
                            {tip}
                        </dd>
                    </dl>
                </div>
                <div className="detail-btns">
                    <a className="icon-btn kefu" href="javascript:void(0);">联系客服</a>
                    <a className="icon-btn like" href="javascript:void(0);" onClick={this.sp_fav} disabled={this.state.loading?"disabled":""}>{this.state.is_fav ? '取消收藏' : '收藏'}</a>
                    <a className="icon-btn share" href="javascript:void(0);">分享</a>
                    <br/><br/>
                    <a className="icon-btn2 buy" href="javascript:void(0);" onClick={this.buy} disabled={this.state.buying?"disabled":""}>立即购买</a>
                    <a className="icon-btn2 add-cart" href="javascript:void(0);" onClick={this.add2cart} disabled={this.state.adding?"disabled":""}>加入购物车</a>
                </div>
                {service}
            </div>
        );}

});

const test_sp_detail = {
    id: 156,
    name: '今日特卖 御宝金装羊奶粉1段0~6个月900g/罐',
    synopsis: '中乳协推荐 低过敏 易吸收 好羊奶 选御宝',
    has_sku: 1,
    sku_id: [
        {id: 100, vid:[101,102,103,104,105]},
        {id:200, vid:[201]}
    ],
    sku_name: [
        {id: '大小', vid: ['XL 适合 175-180cm', 'L 适合 170-175cm', 'M 适合 165-170cm', 'XXL 适合180-185cm', 'XXXL适合185-190cm']}
    ],
    sku_list: [
        {id: 1000, sku_id: '100:101;200:201;', sku_desc: '大小:XL 适合 175-180cm;颜色:黑色;', ori_price: 249, price: 219, quantity: 500},
        {id: 1000, sku_id: '100:102;200:201;', sku_desc: '大小:L 适合 170-175cm;颜色:黑色;', ori_price: 249, price: 229, quantity: 450},
        {id: 1000, sku_id: '100:103;200:201;', sku_desc: '大小:M 适合 165-170cm;颜色:黑色;', ori_price: 249, price: 209, quantity: 400},
        {id: 1000, sku_id: '100:104;200:201;', sku_desc: '大小:XXL 适合180-185cm:黑色;', ori_price: 249, price: 239, quantity: 550},
        {id: 1000, sku_id: '100:105;200:201;', sku_desc: '大小:XXXL适合185-190cm;颜色:黑色;', ori_price: 249, price: 199, quantity: 350}
    ]
};

const test_sp = {
    id: 156,
    name: '今日特卖 御宝金装羊奶粉1段0~6个月900g/罐',
    synopsis: '中乳协推荐 低过敏 易吸收 好羊奶 选御宝',
    has_sku: 0,
    sku_info: {
        id: 1202,
        ori_price: 75.0,
        price: 58.0,
        quantity: 500
    },
    min_ori_price: 75.0,
    min_price: 58.0
};

ReactDOM.render(
    <Detail_wrap data={sp_detail} />,
    document.getElementById('detail_wrap')
);


//商品详情

const DetailTab_wrap = React.createClass({
    render(){
        let children = this.props.children;
        return <div className="detailTab-wrap">{children}</div>;
    }
});

const Main_detail_info = React.createClass({
    componentDidMount(){
        let url = getBaseUrl() + '/mengwu/spdetail/detail_info';
        let data = {
            id: sp_detail.id
        };

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                if(data.info!="") this.refs.detail.innerHTML = data.info;
            }else{
                if(typeof data.info.redirect !="undefined"){
                    window.location.href = data.info.redirect;
                }
            }

        }.bind(this)});
    },
    render(){
        return(
            <DetailTab_wrap>
                <div className="detail-box" ref="detail"><div className="empty-list">该商品没有商品详情</div></div>
            </DetailTab_wrap>
        );
    }
});

//买家口碑
const Evaluate_box = React.createClass({
    getInitialState(){
        return({
            picView: {
                img: "",
                show: false,
                index: -1
            }
        });
    },
    closePicViewer(){
        this.setState({
            picView: {
                img: "",
                show: false
            }
        });
    },
    triggerPicViewer(src,index){
        let picView = this.state.picView;
        if(picView.index==index){
            picView.index = -1;
            picView.show = false;
        }else{
            let i = src.indexOf('&size=');
            if(i!=-1){
                src = src.substr(0,i);
            }
            picView.show = true;
            picView.img = src;
            picView.index = index;
        }
        this.setState(picView);

    },
    render(){
        let data = this.props.data;
        let imgs = this.props.data.img.map(function(val,index){
            return <li><a href="javascript:void(0);" onClick={()=>this.triggerPicViewer(val,index)}><img src={val} /></a></li>;
        }.bind(this));
        let date = new Date(data.createtime*1000);
        let picView = this.state.picView;

        let picViewer_wrap = picView.show ? [
            <div className="pic-viewer">
                <div className="p-tools">
                    <a className="p-putup" href="javascript:void(0)" onClick={this.closePicViewer}>收起</a>
                    <span className="line">|</span>
                    <a className="p-origin" href={picView.img} target="_blank">查看原图</a>
                </div>
                <div className="pic-wrap">
                    {picView.Img=="" ? null : <img src={picView.img} onClick={this.closePicViewer}/>}
                </div>
            </div>
        ] : null;

        return(
            <li className="evaluate-list">
                <div className="head"><img src={data.user_head} /></div>
                <div className="content">
                    <div className="nickname">{data.user_nick}</div>
                    <div className="text"><p>{data.comment}</p></div>
                    <div className="pics">
                        <ul>
                            {imgs}
                        </ul>
                    </div>
                    {picViewer_wrap}
                    <div className="date">{date.getFullYear()+'年'+(date.getMonth()+1)+'月'+date.getDate()+'日' + ' '+date.getHours()+'时'+date.getMinutes()+'分'}</div>
                </div>
            </li>
        );
    }
});

const { Pagination,Spin } = antd;

let PaginationChange = page =>
    (console.log('page:'+page));

const Main_detail_evaluate = React.createClass({
    getInitialState(){
        return({
            data: [],
            nowPage: 1,
            num: 0,
            page_size: 6,
            loading: true

        });
    },
    componentDidMount(){
        this.jumpPage(1);
    },
    jumpPage(page_no){
        let url = getBaseUrl() + '/mengwu/mengwuApi/spdetail_comment';
        let data = {
            pid: sp_detail.id,
            page_no: page_no,
            page_size: this.state.page_size
        };

        this.setState({loading: true});

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({data: data.info.list, num: data.info.count, nowPage: page_no, loading: false});
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
    PaginationChange(page){
        this.setState({loading: true});
        $('html, body').animate({
            scrollTop: $('#main_detail').offset().top
        }, 200);
        this.jumpPage(page);
    },
    render(){
        let list = this.state.data.map(function(val){
            return <Evaluate_box data={val} />
        });
        return(
            <DetailTab_wrap>
                <Spin spinning={this.state.loading}>
                    <div className="evaluate-box">
                        <ul>
                            {list}
                        </ul>
                    </div>
                    {list.length==0 ? <div className="empty-list">没有任何评论</div> : [
                        <div className="pagination-wrap">
                            <div className="pagination-middle">
                                <Pagination current={this.state.nowPage} total={this.state.num} pageSize={this.state.page_size} onChange={this.PaginationChange} />
                            </div>
                        </div>
                    ]}
                </Spin>
            </DetailTab_wrap>
        );

    }
});


//猜你喜欢
const Main_detail_hot = React.createClass({
    getInitialState(){
        return({
            list: [],
            loading: false
        });
    },
    componentDidMount(){
        let url = getBaseUrl() + '/mengwu/spdetail/hot';
        let data = {
            id: sp_detail.id
        };

        this.setState({loading: true});

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({list: data.info, loading: false});
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
    render(){
        const url = getBaseUrl() + '/mengwu/spdetail/index/id/';
        const list = this.state.list.map(function(val){
            return(
                <a className="item" href={url+val.id} target="_blank">
                    <div className="ant-card">
                        <div className="item-wrap">
                            <div className="pic">
                                <img src={val.img}/>
                            </div>
                            <div className="name">{val.name}</div>
                            <div className="price">
                                <em className="rmb">￥</em>{val.price}
                            </div>
                        </div>
                    </div>
                </a>
            );
        });
        return(
            <DetailTab_wrap>
                <Spin spinning={this.state.loading}>
                    <div className="hot-box">
                        {list.length!=0 ?[
                            <div className="lists-wrap">
                                {list}
                            </div>
                        ]:<div className="empty-list">没有任何商品</div>}
                    </div>
                </Spin>
            </DetailTab_wrap>
        );
    }
});


//商品咨询
const Main_detail_consult = React.createClass({
    getInitialState(){
        return({
            comment:'',
            show:false,
            data: [],
            nowPage: 1,
            num: 0,
            page_size: 6,
            loading: true
        });
    },
    commentOnChange(event){
        this.setState({comment:event.target.value});
    },

    toggle(){
        this.setState({show:!this.state.show});
    },
    componentDidMount(){
        this.jumpPage(1);
    },
    jumpPage(page_no){
        let url = getBaseUrl() + '/mengwu/mengwuApi/sp_query_faq';
        let data = {
            pid: sp_detail.id,
            page_no: page_no,
            page_size: this.state.page_size
        };

        this.setState({loading: true});

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({data: data.info.list, num: data.info.count, nowPage: page_no, loading: false});
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
    uploadQues(){
        let url = getBaseUrl() + '/mengwu/mengwuApi/sp_faq';

        if($.trim(this.state.comment)==''){
            message.error('提问内容不能为空哦~~~');
            return;
        }

        let data = {
            pid: sp_detail.id,
            content:this.state.comment
        };

        this.setState({loading: true});
        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                message.success('提交成功！');
                this.setState({comment:'', show:false, loading: false});
                this.jumpPage(1);
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
    cancelUp(){
         this.setState({show:false,comment:''});
    },
    PaginationChange(page){
        this.setState({loading: true});
        $('html, body').animate({
            scrollTop: $('#main_detail').offset().top
        }, 200);
        this.jumpPage(page);
    },
    getDate(time){
        let date = new Date(time*1000);
        return(date.getFullYear()+'年'+(date.getMonth()+1)+'月'+date.getDate()+'日' + ' '+date.getHours()+'时'+date.getMinutes())+'分';
    },
    render(){

        let text = this.state.show?'block':'none';

        let list = this.state.data.map(function(a){
            return (
                <div className="question-area">
                    <div>
                        <div className="head-img"><img src={a.ask_head} /></div>
                        <div className="nickname">{a.ask_nickname}</div>
                    </div>
                    <div className="ques-right">
                        <div className="content">
                            <div className="QA-badge"><img src="./Public/Mengwu/img/spdetail/sp-ask.png" /></div>
                            <div className="text"><p>{a.ask_content}</p></div>
                            <div className="date">{this.getDate(a.ask_time)}</div>
                        </div>
                        {a.reply_time=='0'?null:
                            [<div className="answer-area">
                                <div className="QA-badge"><img src="./Public/Mengwu/img/spdetail/sp-answer.png" /></div>
                                <div className="text"><p>{a.reply_content}</p></div>
                                <div className="date">{this.getDate(a.reply_time)}</div>
                            </div>]}
                    </div>
                </div>
            );
        }.bind(this));
        return(
            <DetailTab_wrap>
                <Spin spinning={this.state.loading}>
                    <div className="consult-box">
                        <div className="empty-list">购买前如有问题，请向萌屋客服咨询<a href="javascript:void(0);" onClick={this.toggle}>去提问</a></div>
                        <div className="ask-area" style={{display:text}}>
                            <textarea id="pre-ques" value={this.state.comment} onChange={this.commentOnChange} />
                            <div className="oper">
                                <a className="btn cancel-btn" onClick={this.cancelUp} >取消</a>
                                <a className="btn upload-btn" onClick={this.uploadQues} >提交问题</a>
                            </div>
                        </div>
                        <div className="faq-wrap">
                            {list}
                            {list.length==0 ? <div className="empty-list">没有任何提问</div> : [
                                <div className="pagination-wrap">
                                    <div className="pagination-middle">
                                        <Pagination current={this.state.nowPage} total={this.state.num} pageSize={this.state.page_size} onChange={this.PaginationChange} />
                                    </div>
                                </div>
                            ]}
                        </div>
                    </div>
                </Spin>
            </DetailTab_wrap>
        );
    }
});


//萌屋优势
const Main_detail_advantage = React.createClass({
    render(){
        return(
            <DetailTab_wrap>
                <div className="advantage-box">
                    <img src={document.getElementsByTagName('base')[0].href+'Public/Mengwu/img/spdetail/youshi.jpg'}/>
                </div>
            </DetailTab_wrap>
        );
    }
});


//问大家
const Main_detail_askevery = React.createClass({
    render(){
        return(
            <DetailTab_wrap>
                <div className="askevery-box">
                    <div className="ask">
                        <em>提出你的疑问，让买过的淘友来帮你解答吧！</em>
                        <a href="javascript:void(0);" className="btn btn-ask">去提问</a>
                    </div>
                </div>
            </DetailTab_wrap>
        );
    }
});


const { Tabs } = antd;
const TabPane = Tabs.TabPane;

function callback(key) {
    // console.log(key);
}
ReactDOM.render(
    <Tabs defaultActiveKey="1" onChange={callback}>
        <TabPane tab="商品详情" key="1"><Main_detail_info /></TabPane>
        <TabPane tab="买家口碑" key="2"><Main_detail_evaluate /></TabPane>
        <TabPane tab="猜你喜欢" key="3"><Main_detail_hot /></TabPane>
        <TabPane tab="商品咨询" key="4"><Main_detail_consult /></TabPane>
        <TabPane tab="萌屋优势" key="5"><Main_detail_advantage /></TabPane>
    </Tabs>
    ,document.getElementById('main_detail')
);