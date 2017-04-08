const Tabpane = React.createClass({
    render(){
        return;
    }
});

const Tabnav = React.createClass({
    onClick(){
        this.props.tabClick(this.props.tab_id);
    },
    render(){
        return(
            <div className={this.props.class} onClick={this.onClick}>{this.props.tab}</div>
        );
    }
});

const Tabs = React.createClass({
    getInitialState(){
        let live = React.Children.map(this.props.children,function(val){
            return false;
        });
        return({now_tab: 0, live: live});
    },
    tabClick(n){
        this.setState({now_tab: n});
    },

    render(){
        let [tab_navs,tab_contents] = [[],[]];
        let children = this.props.children;
        let now_tab = this.state.now_tab;
        let live = this.state.live;

        React.Children.forEach(children,function(val,n){
            let tab_class = "tab-nav";
            let list_class = "tab-tabpane tab-tabpane-hidden";
            let aria_hidden = "false";
            if(now_tab == n){
                tab_class = "tab-nav tab-selected";
                list_class = "tab-tabpane";
                aria_hidden = "true";
            }
            if(now_tab == n)live[n] = true;
            tab_navs.push(<Tabnav {...val.props} tab_id={n} tabClick={this.tabClick} class={tab_class} now_tab={now_tab} />);

            if(live[n])tab_contents.push(<div aria-hidden={aria_hidden} className={list_class}>{val.props.children}</div>);

        }.bind(this));

        return(
            <div className="tab">
                <div className="tab-list">
                    {tab_navs}
                </div>
                <div className="tab-content">
                    {tab_contents}
                </div>
            </div>
        );
    }
});

const { Pagination, Spin, Popconfirm, message } = antd;

const Zen_item = React.createClass({
    render(){
        const data = this.props.data;
        let p = data.loc_province;
        let c = data.loc_city;
        let a = mengwu.address_json;
        let b = _.find(a, ['value' , p]);
        let address = "";
        if(b){
            let pt = b.label;
            let a = _.find(b.children, ['value' , c]);
            if(a) {
                var ct = a.label;
                address = pt+ct;
            }
        }
        let time = this.props.type == 'send'?data.createtime : data.win_time;
        time = new Date(time*1000);
        let date = time.getFullYear() + '年' + time.getMonth() + '月' + time.getDate() + '日 ' + time.getHours() +':'+ time.getMinutes();
        const url = document.getElementsByTagName('base')[0].href;
        const img_list = data.show_imgs ? data.show_imgs.map(function(val){
            return <li><a href="javascript:void(0);"> <img src={val} /></a></li>;
        }) : null;
        return(
            <div className="zengwuxian">
                <div className="user-info">
                    <div className="head"><img src={data.head} /></div>
                    <div className="user-right">
                        <div className="name">{data.nickname}</div>
                        <div class="tp">
                            <span className="time">{date}</span>
                            <span className="pos">{address}</span>
                        </div>
                    </div>
                </div>
                <div className="zcontent">
                    <ul>
                        {img_list}
                    </ul>
                </div>
                <div className="caption">
                    <img src={url+'Public/Mengwu/img/user/favorite/zengwuxian.png'} /> <h4><a href="javascript:void(0);">{data.name}</a></h4>
                </div>
            </div>
        );
    }
});

const Zenwuxian = React.createClass({
    getInitialState(){
        return({
            nowPage: 1,
            num: 0,
            page_size: 5,
            list: [],
            loading: true
        });
    },
    componentDidMount(){
        this.jumpPage(1);
    },
    jumpPage(page_no){
        let url = getBaseUrl() + '/mengwu/mengwuApi/product_listGift';
        let data = {
            page_no: page_no,
            page_size: this.state.page_size,
            type: this.props.type
        };

        _post(url,jQuery.param(data),{data_back: function(data){
            if(data.status){
                this.setState({list: data.info.list, num: data.info.count, loading: false, nowPage: page_no});
            }else{
                if(typeof data.info.redirect !="undefined"){
                    window.location.href = data.info.redirect;
                }
            }
        }.bind(this)});
    },
    PaginationChange(page){
        this.setState({loading: true,list: []});
        $('html, body').animate({
            scrollTop: 0
        }, 200);
        this.jumpPage(page);
    },
    render(){
        let list = this.state.list.map(function(val){
            return(
                <Zen_item data={val} {...this.props}/>
            );
        }.bind(this));

        let tip = this.props.type == 'send' ? '没有赠出任何物品哟~' : '没有收到任何物品哟~';

        let page = [];
        if(!this.state.loading){
            if(this.state.list.length!=0){
                page =
                    <div className="pagination-wrap">
                        <div className="pagination-middle">
                            <Pagination current={this.state.nowPage} total={this.state.num} pageSize={this.state.page_size} onChange={this.PaginationChange} />
                        </div>
                    </div>
            }else{
                list = <div className="text-center" style={{marginTop:50}}>{tip}</div>
            }

        }
        return(
            <Spin spinning={this.state.loading}>
                <div className="post-list">
                    {list}
                    {page}
                </div>
            </Spin>
        );
    }
});

const Baibaoxiang_zwx = React.createClass({
    getDefaultProps(){
        return({data:[]});
    },
    render(){
        return(
            <Tabs>
                <Tabpane tab="我赠出的">
                    <Zenwuxian type="send" />
                </Tabpane>
                <Tabpane tab="我收到的">
                    <Zenwuxian type="receive" />
                </Tabpane>
            </Tabs>
        );
    }
});

ReactDOM.render(
    <Baibaoxiang_zwx />,
    document.getElementById('zwx')
);
