'use strict';

var _antd = antd;
var message = _antd.message;


var Thumb_list = React.createClass({
    displayName: 'Thumb_list',
    onMouseOver: function onMouseOver() {
        this.props.setThumbSelected(this.props.item_pic.id);
    },
    render: function render() {
        return React.createElement(
            'li',
            { 'data-index': this.props.item_pic.id, className: this.props.item_pic.selected ? 'select' : '' },
            React.createElement(
                'div',
                { className: 'pic s50', onMouseOver: this.onMouseOver },
                React.createElement('img', { src: this.props.item_pic.s50 })
            )
        );
    }
});

var Main_pic = React.createClass({
    displayName: 'Main_pic',
    getInitialState: function getInitialState() {

        var zoom_icon = "zoom-icon icon iconfont";

        return { zoom_icon: zoom_icon };
    },
    onMouseMove: function onMouseMove(event) {
        var wrap_rect = this.refs.imagezoom_wrap.getBoundingClientRect();
        var img_rect = this.refs.img.getBoundingClientRect();

        var zoom_lens = this.props.zoom_lens;

        if (event.clientX > img_rect.left && event.clientX < img_rect.right && event.clientY > img_rect.top && event.clientY < img_rect.bottom) {
            zoom_lens.display = 'block';
            zoom_lens.left = event.clientX - zoom_lens.width / 2 - wrap_rect.left;
            zoom_lens.top = event.clientY - zoom_lens.height / 2 - wrap_rect.top;
            zoom_lens.left = zoom_lens.left < img_rect.left - wrap_rect.left ? img_rect.left - wrap_rect.left : zoom_lens.left;
            zoom_lens.left = zoom_lens.left > img_rect.width - zoom_lens.width + img_rect.left - wrap_rect.left ? img_rect.width - zoom_lens.width + img_rect.left - wrap_rect.left : zoom_lens.left;

            zoom_lens.top = zoom_lens.top < img_rect.top - wrap_rect.top ? img_rect.top - wrap_rect.top : zoom_lens.top;
            zoom_lens.top = zoom_lens.top > img_rect.height - zoom_lens.height + img_rect.top - wrap_rect.top ? img_rect.height - zoom_lens.height + img_rect.top - wrap_rect.top : zoom_lens.top;

            zoom_lens.offsetLeft = zoom_lens.left - (img_rect.left - wrap_rect.left);
            zoom_lens.offsetTop = zoom_lens.top - (img_rect.top - wrap_rect.top);
            zoom_lens.img_rect = img_rect;
        }

        this.props.setZoomLens(zoom_lens);

        var zoom_icon = "zoom-icon icon iconfont hide";
        this.setState({ zoom_icon: zoom_icon });
    },
    OnMouseOut: function OnMouseOut() {
        var zoom_lens = this.props.zoom_lens;
        zoom_lens.display = 'none';
        this.props.setZoomLens(zoom_lens);

        var zoom_icon = "zoom-icon icon iconfont";

        this.setState({ zoom_lens: zoom_lens, zoom_icon: zoom_icon });
    },
    render: function render() {
        return React.createElement(
            'div',
            { className: 'main-pic' },
            React.createElement(
                'a',
                { href: 'javascript:void(0);', ref: 'imagezoom_wrap' },
                React.createElement(
                    'div',
                    { className: 'imagezoom-wrap', onMouseMove: this.onMouseMove, onMouseOut: this.OnMouseOut },
                    React.createElement('img', { src: this.props.pic, ref: 'img' }),
                    React.createElement('span', { className: 'imagezoom-lens', style: this.props.zoom_lens })
                )
            ),
            React.createElement(
                'div',
                { className: this.state.zoom_icon },
                ''
            )
        );
    }
});

var Item_gallery = React.createClass({
    displayName: 'Item_gallery',
    getInitialState: function getInitialState() {
        var gallery = this.props.data;
        gallery[0].selected = true;
        var zoom_lens = {
            position: 'absolute',
            display: 'none',
            width: 400 / 950 * 400,
            height: 400 / 950 * 400,
            top: 0,
            left: 0,
            img_rect: {
                width: 400,
                height: 400
            },
            original_rect: {
                width: 950,
                height: 950
            },
            offsetLeft: 0,
            offsetTop: 0

        };

        return { gallery: gallery, main_pic: { s400: gallery[0].s400, original: gallery[0].original }, zoom_lens: zoom_lens };
    },
    setZoomLens: function setZoomLens(p) {

        var original = this.refs.zoom_img.getBoundingClientRect();

        var zoom_lens = this.state.zoom_lens;

        var w = 400 * 400 / original.width;
        var h = 400 * 400 / original.height;

        zoom_lens.width = w < h ? w : h;
        zoom_lens.height = w < h ? w : h;

        p.original_rect = original;

        this.setState({ zoom_lens: p });
    },
    setThumbSelected: function setThumbSelected(id) {
        var main_pic = "";
        var gallery = this.state.gallery.map(function (val) {
            if (val.id == id) {
                main_pic = {
                    s400: val.s400,
                    original: val.original
                };
                val.selected = true;
            } else {
                val.selected = false;
            }
            return val;
        });

        this.setState({ gallery: gallery, main_pic: main_pic });
    },
    render: function render() {
        var thumb_list = this.state.gallery.map(function (val) {
            return React.createElement(Thumb_list, { item_pic: val, setThumbSelected: this.setThumbSelected });
        }.bind(this));

        var zoom_lens = this.state.zoom_lens;

        var img_pos = {
            left: -zoom_lens.offsetLeft * zoom_lens.original_rect.width / zoom_lens.img_rect.width,
            top: -zoom_lens.offsetTop * zoom_lens.original_rect.height / zoom_lens.img_rect.height
        };

        return React.createElement(
            'div',
            { className: 'item-gallery' },
            React.createElement(Main_pic, { pic: this.state.main_pic.s400, setZoomLens: this.setZoomLens, zoom_lens: this.state.zoom_lens }),
            React.createElement(
                'div',
                { className: 'zoom', style: { display: this.state.zoom_lens.display } },
                React.createElement('img', { ref: 'zoom_img', style: img_pos, src: this.state.main_pic.original })
            ),
            React.createElement(
                'ul',
                { className: 'thumb' },
                thumb_list
            )
        );
    }
});

// const gallery_list = [
//     {id: 1, s50: "//gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg_50x50.jpg", s400: "https://gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg_400x400.jpg_.webp", original:"https://gd4.alicdn.com/bao/uploaded/i4/TB1x3KyKXXXXXc_XpXXXXXXXXXX_!!0-item_pic.jpg"},
//     {id: 2, s50: "//gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg_50x50.jpg", s400: "https://gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg_400x400.jpg_.webp", original:"https://gd3.alicdn.com/imgextra/i3/62598785/TB2ljQGeVXXXXXFXpXXXXXXXXXX_!!62598785.jpg"}
// ];


ReactDOM.render(React.createElement(Item_gallery, { data: sp_detail.gallery_list }), document.getElementById('item_gallery'));

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
            var max = this.props.max;
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

var Sku_prop = React.createClass({
    displayName: 'Sku_prop',
    onClick: function onClick() {
        var sku = this.props.sku;
        this.props.sku_click(sku.id, sku.vid);
    },
    render: function render() {
        var sku = this.props.sku;
        return React.createElement(
            'li',
            { className: this.props.selected ? 'selected' : '', onClick: this.onClick },
            React.createElement(
                'a',
                { href: 'javascript:void(0);' },
                React.createElement(
                    'span',
                    null,
                    sku.name
                )
            )
        );
    }
});

var Detail_wrap = React.createClass({
    displayName: 'Detail_wrap',
    getInitialState: function getInitialState() {
        if (typeof this.props.data == 'undefined') {
            return {};
        }
        var state = { sku: {}, stock: "", quantity: 1, psku_id: 0 };
        var detail = this.props.data;
        if (detail.has_sku == 1) {
            state.sku = detail.sku_id.map(function (v) {
                return { id: v.id, vid: 0 };
            });
        } else {
            state.stock = detail.sku_info.quantity;
            state.psku_id = detail.sku_info.id;
        }
        return { state: state, adding: false, buying: false, is_fav: parseInt(detail.is_fav) ? true : false, loading: false };
    },
    sku_click: function sku_click(id, vid) {
        var state = this.state.state;
        state.sku = state.sku.map(function (v) {
            if (v.id == id) {
                if (v.vid == vid) {
                    v.vid = 0; //取消选中
                } else {
                    v.vid = vid; //选中
                }
            }
            return v;
        });

        var sku_detail = this.calc_detail();
        state.stock = sku_detail.stock;
        state.psku_id = sku_detail.psku_id;

        this.setState({ state: state });
    },
    calc_detail: function calc_detail() {
        var detail = this.props.data;
        var price = "";
        var ori_price = "";
        var ifselected = true;
        var state = this.state.state;
        var sku_id = "";
        var stock = "";
        var psku_id = 0;
        var type = "";
        state.sku.forEach(function (v) {
            if (v.vid != 0) {
                sku_id += v.id + ':' + v.vid + ';';
            } else {
                ifselected = false;
            }
        });

        //判断分组,获取分组
        var group_info = null;
        if (detail.gid != '') {
            var g = detail.group_info ? detail.group_info : [];
            group_info = _.find(g, ['group', detail.gid]);
        }

        if (!ifselected) {
            (function () {
                //计算未选中时价格
                var min_price = 0;
                var max_price = 0;
                detail.sku_list.forEach(function (val) {
                    if (min_price == 0) {
                        min_price = parseFloat(val.price);
                    } else if (parseFloat(val.price) > min_price) {
                        max_price = parseFloat(val.price);
                    }
                });

                if (min_price == max_price || max_price == 0) {
                    price = min_price.toFixed(2);
                } else if (max_price != 0) {
                    price = min_price.toFixed(2) + '~' + max_price.toFixed(2);
                }
            })();
        } else {
            //计算选中时价格

            detail.sku_list.forEach(function (v) {
                if (v.sku_id == sku_id) {
                    //有分组则采用分组价
                    var gs = null; //分组sku信息
                    if (group_info) {
                        if (group_info.sku_info) {
                            gs = _.find(group_info.sku_info, ['sku_id', sku_id]);
                        }
                    }
                    if (gs) {
                        if (gs.price > 0) {
                            price = gs.price;
                            type = "优惠价:";
                        } else {
                            //分组价格为0时采用普通价格 或会员价格
                            if (parseFloat(v.member_price) > 0) {
                                type = "会员价:";
                                price = parseFloat(v.member_price);
                            } else {
                                price = v.price;
                            }
                        }
                    } else {
                        //不使用分组时采用普通价格 或会员价格
                        if (parseFloat(v.member_price) > 0) {
                            type = "会员价:";
                            price = v.member_price;
                        } else {
                            price = v.price;
                        }
                    }
                    ori_price = v.ori_price;
                    stock = v.quantity;
                    psku_id = v.id;
                }
            });
        }

        return { price: { price: price, ori_price: ori_price, type: type }, stock: stock, psku_id: psku_id };
    },
    changeQuantity: function changeQuantity(q) {
        var state = this.state.state;
        state.quantity = q;
        this.setState({ state: state, tip: "" });
    },
    selectOk: function selectOk() {
        //判断可否执行加入购物车和购买操作
        var state = this.state.state;
        var detail = this.props.data;
        var psku_id = state.psku_id;
        var tip = "";
        var add = true;
        if (detail.has_sku == 1) {
            var sku = state.sku;
            if (Array.isArray(sku)) {
                sku.forEach(function (val) {
                    if (val.vid == 0) {
                        add = false;
                        tip = "请先选择规格!";
                    }
                });
                if (psku_id > 0) {
                    var sku_info = _.find(detail.sku_list, { id: psku_id });
                    if (sku_info.quantity <= 0) {
                        add = false;
                        tip = "商品该规格库存不足!";
                    }
                }
            } else {
                add = false;
                tip = "规格出错，无法加入购物车!";
            }
        } else {
            var _sku_info = detail.sku_info;
            if (_sku_info.quantity <= 0) {
                add = false;
                tip = "商品该规格库存不足!";
            }
        }

        if (!add) {
            this.setState({ tip: tip });
            return false;
        } else {
            return true;
        }
    },
    buy: function buy() {
        //立即购买
        var state = this.state.state;
        var detail = this.props.data;
        if (!this.selectOk()) return;

        var buying = message.loading('加载中...', 0);

        this.setState({ buying: buying }); //禁止立即购买按钮

        var form = $('<form></form>');
        var action = getBaseUrl() + '/mengwu/order/confirm';
        form.attr({ action: action, method: 'post', target: '_self' });
        var pid_input = $('<input type="text" name="pid" />');
        var count_input = $('<input type="text" name="count" />');
        var psku_id_input = $('<input type="text" name="psku_id" />');
        var from_input = $('<input type="text" name="from" value="buy" />');
        pid_input.attr('value', detail.id);
        count_input.attr('value', state.quantity);
        psku_id_input.attr('value', state.psku_id);
        form.append(pid_input, count_input, psku_id_input, from_input);
        $('body').append(form);
        form.submit().remove();
    },
    add2cart: function add2cart() {
        //加入购物车
        var state = this.state.state;
        var detail = this.props.data;
        if (!this.selectOk()) return;

        this.setState({ adding: true }); //禁止加入购物车按钮
        var url = getBaseUrl() + '/mengwu/mengwuApi/spcart_add';
        var data = {
            pid: detail.id,
            count: state.quantity,
            psku_id: state.psku_id,
            group_id: detail.gid
        };

        _post(url, jQuery.param(data), { data_back: this.addsp_response });
    },
    addsp_response: function addsp_response(data) {
        if (data.status) {
            message.success('添加成功!');
            this.setState({ adding: false }); //取消禁止加入购物车按钮
            setTimeout(function () {
                window.location.reload();
            }, 1000);
        } else {
            if (typeof data.info.redirect != "undefined") {
                message.error(data.info.info);
                setTimeout(function () {
                    window.location.href = data.info.redirect;
                }, 1000);
            } else {
                message.error(data.info);
                this.setState({ adding: false }); //取消禁止加入购物车按钮
            }
        }
    },
    sp_fav: function sp_fav() {
        //商品收藏

        if (this.state.loading) return;

        var url = getBaseUrl() + '/mengwu/mengwuApi/product_favorites';
        var is_fav = this.state.is_fav;
        var data = {
            pid: this.props.data.id,
            value: is_fav ? 0 : 1
        };

        this.setState({ loading: true });
        _post(url, jQuery.param(data), { data_back: function (data) {
                this.setState({ loading: false });
                if (data.status) {
                    this.setState({ is_fav: !is_fav });
                    message.success(data.info);
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        message.error(data.info.info);
                        setTimeout(function () {
                            window.location.href = data.info.redirect;
                        }, 1000);
                    }
                }
            }.bind(this) });
    },
    render: function render() {
        if (typeof this.props.data == 'undefined') {
            return React.createElement(
                'div',
                { className: 'detail-wrap' },
                React.createElement(
                    'div',
                    { className: 'title' },
                    React.createElement(
                        'h3',
                        { className: 'main-title' },
                        '商品数据错误'
                    )
                )
            );
        }
        var detail = this.props.data;
        var price = {};
        var sku_lists = [];
        var sku_detail = {};
        var quantity = this.state.state.quantity;
        var tip = React.createElement(
            'p',
            null,
            this.state.tip
        );

        if (detail.has_sku == 1) {
            //有多规格

            sku_detail = this.calc_detail();

            price = sku_detail.price;

            sku_lists = detail.sku_name.map(function (v, n) {

                var sku_id = detail.sku_id[n];
                var sku = v.vid.map(function (x, n) {
                    var selected = false;
                    var id = sku_id.id,
                        vid = sku_id.vid[n];
                    this.state.state.sku.forEach(function (v) {
                        if (v.id == id && v.vid == vid) selected = true;
                    });

                    return React.createElement(Sku_prop, { sku: { id: id, vid: vid, name: x }, sku_click: this.sku_click, selected: selected });
                }.bind(this));
                return React.createElement(
                    'dl',
                    { className: 'proptype clear' },
                    React.createElement(
                        'dt',
                        null,
                        v.id
                    ),
                    React.createElement(
                        'dd',
                        null,
                        React.createElement(
                            'ul',
                            null,
                            sku
                        )
                    )
                );
            }.bind(this));
        } else {
            //没有多规格

            //判断分组,获取分组
            var group_info = null;
            if (detail.gid != '') {
                var g = detail.group_info ? detail.group_info : [];
                group_info = _.find(g, ['group', detail.gid]);
            }

            //有分组则采用分组价
            var gs = null; //分组sku信息
            var sku_info = detail.sku_info;
            var sku_id = sku_info.id;
            if (group_info) {
                if (group_info.sku_info) {
                    gs = _.find(group_info.sku_info, ['sku_id', sku_id]);
                }
            }
            if (gs) {
                if (gs.price > 0) {
                    price.price = gs.price;
                    price.type = "优惠价:";
                } else {
                    //分组价格为0时采用普通价格 或会员价格
                    if (sku_info.member_price != "") {
                        price.type = "会员价:";
                        price.price = sku_info.member_price;
                    } else {
                        price.price = sku_info.price;
                    }
                }
            } else {
                //不使用分组时采用普通价格 或会员价格
                if (parseFloat(sku_info.member_price) > 0) {
                    price.type = "会员价:";
                    price.price = parseFloat(sku_info.member_price);
                } else {
                    price.price = sku_info.price;
                }
            }

            price.ori_price = sku_info.ori_price;
            sku_detail.stock = sku_info.quantity;
        }
        if (sku_detail.stock != '' && sku_detail.stock < quantity) {
            tip = React.createElement(
                'p',
                null,
                '您所填写的宝贝数量超过库存！'
            );
        }
        if (sku_detail.stock != '' && sku_detail.stock < quantity) {
            tip = React.createElement(
                'p',
                null,
                '您所填写的宝贝数量超过库存！'
            );
        }

        var buy_limit = parseInt(detail.buy_limit);

        var buylimit_prop = function () {
            if (buy_limit > 0) {
                if (buy_limit < quantity) tip = React.createElement(
                    'p',
                    null,
                    '该商品限购',
                    buy_limit,
                    '件！'
                );
                return React.createElement(
                    'div',
                    { className: 'promo' },
                    React.createElement(
                        'dl',
                        { className: 'proptype clear' },
                        React.createElement(
                            'dt',
                            null,
                            '限购:'
                        ),
                        React.createElement(
                            'dd',
                            null,
                            parseInt(buy_limit),
                            '件'
                        )
                    )
                );
            }
        }();

        // 获取服务信息
        var baoyou = false; //是否包邮
        var max = buy_limit > 0 ? buy_limit : this.state.state.stock;
        var service_hash = !detail.service_info ? null : {
            6102: '包邮',
            6103: '24小时发货',
            6104: '退货补运费',
            6105: '假就赔',
            6106: '贵就赔',
            6107: '慢就赔'
        };

        var service_list = [];
        if (service_hash) {
            detail.service_info.forEach(function (val) {
                if (service_hash[val]) {
                    if (service_hash[val] == '包邮') baoyou = true;
                    service_list.push(service_hash[val]);
                }
            });
        }

        var service = service_list.length == 0 ? null : [React.createElement(
            'div',
            { className: 'service' },
            React.createElement(
                'dl',
                { className: 'proptype clear' },
                React.createElement(
                    'dt',
                    null,
                    '服务:'
                ),
                React.createElement(
                    'dd',
                    null,
                    service_list.map(function (val) {
                        return React.createElement(
                            'span',
                            { className: 'service-item' },
                            val
                        );
                    })
                )
            )
        )];
        //店铺优惠信息
        var store_benefit = [];
        if (detail.store_benefit) {
            var s = detail.store_benefit;
            var time = new Date().getTime();
            if (s.start_time * 1000 <= time && time <= s.end_time * 1000) {
                if (s.discount_money > 0) {
                    store_benefit.push(React.createElement(
                        'div',
                        { className: 'promos' },
                        React.createElement(
                            'span',
                            { className: 'detail-icon reduce' },
                            '减'
                        ),
                        ' ',
                        '满' + s.condition + '元减' + s.discount_money
                    ));
                }
                if (s.free_shipping) {
                    store_benefit.push(React.createElement(
                        'div',
                        { className: 'promos' },
                        React.createElement(
                            'span',
                            { className: 'detail-icon nonPostfee' },
                            '包邮'
                        ),
                        ' ',
                        '满' + s.condition + '元包邮'
                    ));
                }
            }
        }
        var promo = store_benefit.length == 0 ? null : [React.createElement(
            'div',
            { className: 'promo' },
            React.createElement(
                'dl',
                { className: 'proptype clear' },
                React.createElement(
                    'dt',
                    null,
                    '优惠:'
                ),
                React.createElement(
                    'dd',
                    null,
                    store_benefit
                )
            )
        )];
        return React.createElement(
            'div',
            { className: 'detail-wrap' },
            React.createElement(
                'div',
                { className: 'title' },
                React.createElement(
                    'h3',
                    { className: 'main-title' },
                    detail.name
                ),
                React.createElement(
                    'p',
                    { className: 'subtitle' },
                    detail.synopsis
                )
            ),
            React.createElement(
                'div',
                { className: 'meta' },
                React.createElement(
                    'span',
                    { className: 'PromoPrice' },
                    price.type,
                    React.createElement(
                        'em',
                        { className: 'rmb' },
                        '￥'
                    ),
                    price.price
                ),
                baoyou ? React.createElement(
                    'span',
                    null,
                    ' ',
                    React.createElement(
                        'span',
                        { className: 'detail-icon nonPostfee' },
                        '包邮'
                    ),
                    ' '
                ) : null,
                price.ori_price != '' ? React.createElement(
                    'span',
                    { className: 'OriPrice' },
                    '原价:',
                    React.createElement(
                        'del',
                        null,
                        React.createElement(
                            'em',
                            { className: 'rmb' },
                            '￥'
                        ),
                        price.ori_price
                    )
                ) : ''
            ),
            promo,
            buylimit_prop,
            React.createElement(
                'div',
                { className: 'skin' },
                sku_lists,
                React.createElement(
                    'dl',
                    { className: 'proptype clear' },
                    React.createElement(
                        'dt',
                        null,
                        '数量:'
                    ),
                    React.createElement(
                        'dd',
                        null,
                        React.createElement(Num_selector, { quantity: 1, changeQuantity: this.changeQuantity, max: max }),
                        React.createElement(
                            'span',
                            { className: 'stock' },
                            '件 ',
                            sku_detail.stock != '' ? '(库存' + sku_detail.stock + '件)' : ''
                        ),
                        tip
                    )
                )
            ),
            React.createElement(
                'div',
                { className: 'detail-btns' },
                React.createElement(
                    'a',
                    { className: 'icon-btn kefu', href: 'javascript:void(0);' },
                    '联系客服'
                ),
                React.createElement(
                    'a',
                    { className: 'icon-btn like', href: 'javascript:void(0);', onClick: this.sp_fav, disabled: this.state.loading ? "disabled" : "" },
                    this.state.is_fav ? '取消收藏' : '收藏'
                ),
                React.createElement(
                    'a',
                    { className: 'icon-btn share', href: 'javascript:void(0);' },
                    '分享'
                ),
                React.createElement('br', null),
                React.createElement('br', null),
                React.createElement(
                    'a',
                    { className: 'icon-btn2 buy', href: 'javascript:void(0);', onClick: this.buy, disabled: this.state.buying ? "disabled" : "" },
                    '立即购买'
                ),
                React.createElement(
                    'a',
                    { className: 'icon-btn2 add-cart', href: 'javascript:void(0);', onClick: this.add2cart, disabled: this.state.adding ? "disabled" : "" },
                    '加入购物车'
                )
            ),
            service
        );
    }
});

var test_sp_detail = {
    id: 156,
    name: '今日特卖 御宝金装羊奶粉1段0~6个月900g/罐',
    synopsis: '中乳协推荐 低过敏 易吸收 好羊奶 选御宝',
    has_sku: 1,
    sku_id: [{ id: 100, vid: [101, 102, 103, 104, 105] }, { id: 200, vid: [201] }],
    sku_name: [{ id: '大小', vid: ['XL 适合 175-180cm', 'L 适合 170-175cm', 'M 适合 165-170cm', 'XXL 适合180-185cm', 'XXXL适合185-190cm'] }],
    sku_list: [{ id: 1000, sku_id: '100:101;200:201;', sku_desc: '大小:XL 适合 175-180cm;颜色:黑色;', ori_price: 249, price: 219, quantity: 500 }, { id: 1000, sku_id: '100:102;200:201;', sku_desc: '大小:L 适合 170-175cm;颜色:黑色;', ori_price: 249, price: 229, quantity: 450 }, { id: 1000, sku_id: '100:103;200:201;', sku_desc: '大小:M 适合 165-170cm;颜色:黑色;', ori_price: 249, price: 209, quantity: 400 }, { id: 1000, sku_id: '100:104;200:201;', sku_desc: '大小:XXL 适合180-185cm:黑色;', ori_price: 249, price: 239, quantity: 550 }, { id: 1000, sku_id: '100:105;200:201;', sku_desc: '大小:XXXL适合185-190cm;颜色:黑色;', ori_price: 249, price: 199, quantity: 350 }]
};

var test_sp = {
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

ReactDOM.render(React.createElement(Detail_wrap, { data: sp_detail }), document.getElementById('detail_wrap'));

//商品详情

var DetailTab_wrap = React.createClass({
    displayName: 'DetailTab_wrap',
    render: function render() {
        var children = this.props.children;
        return React.createElement(
            'div',
            { className: 'detailTab-wrap' },
            children
        );
    }
});

var Main_detail_info = React.createClass({
    displayName: 'Main_detail_info',
    componentDidMount: function componentDidMount() {
        var url = getBaseUrl() + '/mengwu/spdetail/detail_info';
        var data = {
            id: sp_detail.id
        };

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    if (data.info != "") this.refs.detail.innerHTML = data.info;
                } else {
                    if (typeof data.info.redirect != "undefined") {
                        window.location.href = data.info.redirect;
                    }
                }
            }.bind(this) });
    },
    render: function render() {
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                'div',
                { className: 'detail-box', ref: 'detail' },
                React.createElement(
                    'div',
                    { className: 'empty-list' },
                    '该商品没有商品详情'
                )
            )
        );
    }
});

//买家口碑
var Evaluate_box = React.createClass({
    displayName: 'Evaluate_box',
    getInitialState: function getInitialState() {
        return {
            picView: {
                img: "",
                show: false,
                index: -1
            }
        };
    },
    closePicViewer: function closePicViewer() {
        this.setState({
            picView: {
                img: "",
                show: false
            }
        });
    },
    triggerPicViewer: function triggerPicViewer(src, index) {
        var picView = this.state.picView;
        if (picView.index == index) {
            picView.index = -1;
            picView.show = false;
        } else {
            var i = src.indexOf('&size=');
            if (i != -1) {
                src = src.substr(0, i);
            }
            picView.show = true;
            picView.img = src;
            picView.index = index;
        }
        this.setState(picView);
    },
    render: function render() {
        var data = this.props.data;
        var imgs = this.props.data.img.map(function (val, index) {
            var _this = this;

            return React.createElement(
                'li',
                null,
                React.createElement(
                    'a',
                    { href: 'javascript:void(0);', onClick: function onClick() {
                            return _this.triggerPicViewer(val, index);
                        } },
                    React.createElement('img', { src: val })
                )
            );
        }.bind(this));
        var date = new Date(data.createtime * 1000);
        var picView = this.state.picView;

        var picViewer_wrap = picView.show ? [React.createElement(
            'div',
            { className: 'pic-viewer' },
            React.createElement(
                'div',
                { className: 'p-tools' },
                React.createElement(
                    'a',
                    { className: 'p-putup', href: 'javascript:void(0)', onClick: this.closePicViewer },
                    '收起'
                ),
                React.createElement(
                    'span',
                    { className: 'line' },
                    '|'
                ),
                React.createElement(
                    'a',
                    { className: 'p-origin', href: picView.img, target: '_blank' },
                    '查看原图'
                )
            ),
            React.createElement(
                'div',
                { className: 'pic-wrap' },
                picView.Img == "" ? null : React.createElement('img', { src: picView.img, onClick: this.closePicViewer })
            )
        )] : null;

        return React.createElement(
            'li',
            { className: 'evaluate-list' },
            React.createElement(
                'div',
                { className: 'head' },
                React.createElement('img', { src: data.user_head })
            ),
            React.createElement(
                'div',
                { className: 'content' },
                React.createElement(
                    'div',
                    { className: 'nickname' },
                    data.user_nick
                ),
                React.createElement(
                    'div',
                    { className: 'text' },
                    React.createElement(
                        'p',
                        null,
                        data.comment
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'pics' },
                    React.createElement(
                        'ul',
                        null,
                        imgs
                    )
                ),
                picViewer_wrap,
                React.createElement(
                    'div',
                    { className: 'date' },
                    date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日' + ' ' + date.getHours() + '时' + date.getMinutes() + '分'
                )
            )
        );
    }
});

var _antd2 = antd;
var Pagination = _antd2.Pagination;
var Spin = _antd2.Spin;


var PaginationChange = function PaginationChange(page) {
    return console.log('page:' + page);
};

var Main_detail_evaluate = React.createClass({
    displayName: 'Main_detail_evaluate',
    getInitialState: function getInitialState() {
        return {
            data: [],
            nowPage: 1,
            num: 0,
            page_size: 6,
            loading: true

        };
    },
    componentDidMount: function componentDidMount() {
        this.jumpPage(1);
    },
    jumpPage: function jumpPage(page_no) {
        var url = getBaseUrl() + '/mengwu/mengwuApi/spdetail_comment';
        var data = {
            pid: sp_detail.id,
            page_no: page_no,
            page_size: this.state.page_size
        };

        this.setState({ loading: true });

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ data: data.info.list, num: data.info.count, nowPage: page_no, loading: false });
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
    PaginationChange: function PaginationChange(page) {
        this.setState({ loading: true });
        $('html, body').animate({
            scrollTop: $('#main_detail').offset().top
        }, 200);
        this.jumpPage(page);
    },
    render: function render() {
        var list = this.state.data.map(function (val) {
            return React.createElement(Evaluate_box, { data: val });
        });
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                Spin,
                { spinning: this.state.loading },
                React.createElement(
                    'div',
                    { className: 'evaluate-box' },
                    React.createElement(
                        'ul',
                        null,
                        list
                    )
                ),
                list.length == 0 ? React.createElement(
                    'div',
                    { className: 'empty-list' },
                    '没有任何评论'
                ) : [React.createElement(
                    'div',
                    { className: 'pagination-wrap' },
                    React.createElement(
                        'div',
                        { className: 'pagination-middle' },
                        React.createElement(Pagination, { current: this.state.nowPage, total: this.state.num, pageSize: this.state.page_size, onChange: this.PaginationChange })
                    )
                )]
            )
        );
    }
});

//猜你喜欢
var Main_detail_hot = React.createClass({
    displayName: 'Main_detail_hot',
    getInitialState: function getInitialState() {
        return {
            list: [],
            loading: false
        };
    },
    componentDidMount: function componentDidMount() {
        var url = getBaseUrl() + '/mengwu/spdetail/hot';
        var data = {
            id: sp_detail.id
        };

        this.setState({ loading: true });

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ list: data.info, loading: false });
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
    render: function render() {
        var url = getBaseUrl() + '/mengwu/spdetail/index/id/';
        var list = this.state.list.map(function (val) {
            return React.createElement(
                'a',
                { className: 'item', href: url + val.id, target: '_blank' },
                React.createElement(
                    'div',
                    { className: 'ant-card' },
                    React.createElement(
                        'div',
                        { className: 'item-wrap' },
                        React.createElement(
                            'div',
                            { className: 'pic' },
                            React.createElement('img', { src: val.img })
                        ),
                        React.createElement(
                            'div',
                            { className: 'name' },
                            val.name
                        ),
                        React.createElement(
                            'div',
                            { className: 'price' },
                            React.createElement(
                                'em',
                                { className: 'rmb' },
                                '￥'
                            ),
                            val.price
                        )
                    )
                )
            );
        });
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                Spin,
                { spinning: this.state.loading },
                React.createElement(
                    'div',
                    { className: 'hot-box' },
                    list.length != 0 ? [React.createElement(
                        'div',
                        { className: 'lists-wrap' },
                        list
                    )] : React.createElement(
                        'div',
                        { className: 'empty-list' },
                        '没有任何商品'
                    )
                )
            )
        );
    }
});

//商品咨询
var Main_detail_consult = React.createClass({
    displayName: 'Main_detail_consult',
    getInitialState: function getInitialState() {
        return {
            comment: '',
            show: false,
            data: [],
            nowPage: 1,
            num: 0,
            page_size: 6,
            loading: true
        };
    },
    commentOnChange: function commentOnChange(event) {
        this.setState({ comment: event.target.value });
    },
    toggle: function toggle() {
        this.setState({ show: !this.state.show });
    },
    componentDidMount: function componentDidMount() {
        this.jumpPage(1);
    },
    jumpPage: function jumpPage(page_no) {
        var url = getBaseUrl() + '/mengwu/mengwuApi/sp_query_faq';
        var data = {
            pid: sp_detail.id,
            page_no: page_no,
            page_size: this.state.page_size
        };

        this.setState({ loading: true });

        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    this.setState({ data: data.info.list, num: data.info.count, nowPage: page_no, loading: false });
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
    uploadQues: function uploadQues() {
        var url = getBaseUrl() + '/mengwu/mengwuApi/sp_faq';

        if ($.trim(this.state.comment) == '') {
            message.error('提问内容不能为空哦~~~');
            return;
        }

        var data = {
            pid: sp_detail.id,
            content: this.state.comment
        };

        this.setState({ loading: true });
        _post(url, jQuery.param(data), { data_back: function (data) {
                if (data.status) {
                    message.success('提交成功！');
                    this.setState({ comment: '', show: false, loading: false });
                    this.jumpPage(1);
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
    cancelUp: function cancelUp() {
        this.setState({ show: false, comment: '' });
    },
    PaginationChange: function PaginationChange(page) {
        this.setState({ loading: true });
        $('html, body').animate({
            scrollTop: $('#main_detail').offset().top
        }, 200);
        this.jumpPage(page);
    },
    getDate: function getDate(time) {
        var date = new Date(time * 1000);
        return date.getFullYear() + '年' + (date.getMonth() + 1) + '月' + date.getDate() + '日' + ' ' + date.getHours() + '时' + date.getMinutes() + '分';
    },
    render: function render() {

        var text = this.state.show ? 'block' : 'none';

        var list = this.state.data.map(function (a) {
            return React.createElement(
                'div',
                { className: 'question-area' },
                React.createElement(
                    'div',
                    null,
                    React.createElement(
                        'div',
                        { className: 'head-img' },
                        React.createElement('img', { src: a.ask_head })
                    ),
                    React.createElement(
                        'div',
                        { className: 'nickname' },
                        a.ask_nickname
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'ques-right' },
                    React.createElement(
                        'div',
                        { className: 'content' },
                        React.createElement(
                            'div',
                            { className: 'QA-badge' },
                            React.createElement('img', { src: './Public/Mengwu/img/spdetail/sp-ask.png' })
                        ),
                        React.createElement(
                            'div',
                            { className: 'text' },
                            React.createElement(
                                'p',
                                null,
                                a.ask_content
                            )
                        ),
                        React.createElement(
                            'div',
                            { className: 'date' },
                            this.getDate(a.ask_time)
                        )
                    ),
                    a.reply_time == '0' ? null : [React.createElement(
                        'div',
                        { className: 'answer-area' },
                        React.createElement(
                            'div',
                            { className: 'QA-badge' },
                            React.createElement('img', { src: './Public/Mengwu/img/spdetail/sp-answer.png' })
                        ),
                        React.createElement(
                            'div',
                            { className: 'text' },
                            React.createElement(
                                'p',
                                null,
                                a.reply_content
                            )
                        ),
                        React.createElement(
                            'div',
                            { className: 'date' },
                            this.getDate(a.reply_time)
                        )
                    )]
                )
            );
        }.bind(this));
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                Spin,
                { spinning: this.state.loading },
                React.createElement(
                    'div',
                    { className: 'consult-box' },
                    React.createElement(
                        'div',
                        { className: 'empty-list' },
                        '购买前如有问题，请向萌屋客服咨询',
                        React.createElement(
                            'a',
                            { href: 'javascript:void(0);', onClick: this.toggle },
                            '去提问'
                        )
                    ),
                    React.createElement(
                        'div',
                        { className: 'ask-area', style: { display: text } },
                        React.createElement('textarea', { id: 'pre-ques', value: this.state.comment, onChange: this.commentOnChange }),
                        React.createElement(
                            'div',
                            { className: 'oper' },
                            React.createElement(
                                'a',
                                { className: 'btn cancel-btn', onClick: this.cancelUp },
                                '取消'
                            ),
                            React.createElement(
                                'a',
                                { className: 'btn upload-btn', onClick: this.uploadQues },
                                '提交问题'
                            )
                        )
                    ),
                    React.createElement(
                        'div',
                        { className: 'faq-wrap' },
                        list,
                        list.length == 0 ? React.createElement(
                            'div',
                            { className: 'empty-list' },
                            '没有任何提问'
                        ) : [React.createElement(
                            'div',
                            { className: 'pagination-wrap' },
                            React.createElement(
                                'div',
                                { className: 'pagination-middle' },
                                React.createElement(Pagination, { current: this.state.nowPage, total: this.state.num, pageSize: this.state.page_size, onChange: this.PaginationChange })
                            )
                        )]
                    )
                )
            )
        );
    }
});

//萌屋优势
var Main_detail_advantage = React.createClass({
    displayName: 'Main_detail_advantage',
    render: function render() {
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                'div',
                { className: 'advantage-box' },
                React.createElement('img', { src: document.getElementsByTagName('base')[0].href + 'Public/Mengwu/img/spdetail/youshi.jpg' })
            )
        );
    }
});

//问大家
var Main_detail_askevery = React.createClass({
    displayName: 'Main_detail_askevery',
    render: function render() {
        return React.createElement(
            DetailTab_wrap,
            null,
            React.createElement(
                'div',
                { className: 'askevery-box' },
                React.createElement(
                    'div',
                    { className: 'ask' },
                    React.createElement(
                        'em',
                        null,
                        '提出你的疑问，让买过的淘友来帮你解答吧！'
                    ),
                    React.createElement(
                        'a',
                        { href: 'javascript:void(0);', className: 'btn btn-ask' },
                        '去提问'
                    )
                )
            )
        );
    }
});

var _antd3 = antd;
var Tabs = _antd3.Tabs;

var TabPane = Tabs.TabPane;

function callback(key) {
    // console.log(key);
}
ReactDOM.render(React.createElement(
    Tabs,
    { defaultActiveKey: '1', onChange: callback },
    React.createElement(
        TabPane,
        { tab: '商品详情', key: '1' },
        React.createElement(Main_detail_info, null)
    ),
    React.createElement(
        TabPane,
        { tab: '买家口碑', key: '2' },
        React.createElement(Main_detail_evaluate, null)
    ),
    React.createElement(
        TabPane,
        { tab: '猜你喜欢', key: '3' },
        React.createElement(Main_detail_hot, null)
    ),
    React.createElement(
        TabPane,
        { tab: '商品咨询', key: '4' },
        React.createElement(Main_detail_consult, null)
    ),
    React.createElement(
        TabPane,
        { tab: '萌屋优势', key: '5' },
        React.createElement(Main_detail_advantage, null)
    )
), document.getElementById('main_detail'));

//# sourceMappingURL=common.js.map