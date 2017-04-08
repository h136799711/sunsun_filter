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

const Yu_item = React.createClass({
    render(){
        const data = this.props.data;
        const fenlei = data.fid == 7 ? '热门分享' : data.fid == 8 ? '宝妈提问' : '';
        const type = data.fid == 7 ? 'share' : data.fid == 8 ? 'question' : '';
        const url = document.getElementsByTagName('base')[0].href;
        const t_url = getBaseUrl()+'/mengwu/yuerjing/detail/type/'+type+'/tid/'+data.tid;
        return(
            <div className="yuerjing">
                <div className="user-info">
                    <div className="head"><img src={data.head} /></div>
                    <div className="user-right">
                        <div className="name">{data.author}</div>
                        <div className="reason">
                            <div className="text"><a href={t_url} target="_blank">{data.subject||'无标题'}</a></div>
                            <div className="fenlei">{fenlei}</div>
                        </div>
                    </div>
                </div>
                <div className="yuanwen"><a href={t_url} target="_blank">{data.message}</a></div>
            </div>
        );
    }
});

const Yuerjing = React.createClass({
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
        let url = getBaseUrl() + '/mengwu/mengwuApi/baibaoxiang_yuerjing/type/'+this.props.type;
        let data = {
            page_no: page_no,
            page_size: this.state.page_size
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
                <Yu_item data={val}/>
            );
        }.bind(this));
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
                list = <div className="text-center" style={{marginTop:50}}>没有发表任何帖子哟~</div>
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

const Baibaoxiang_yej = React.createClass({
    getDefaultProps(){
        return({data:[]});
    },
    render(){
        return(
            <Tabs>
                <Tabpane tab="热门分享">
                    <Yuerjing type="share" />
                </Tabpane>
                <Tabpane tab="宝妈提问">
                    <Yuerjing type="question" />
                </Tabpane>
            </Tabs>
        );
    }
});

ReactDOM.render(
    <Baibaoxiang_yej />,
    document.getElementById('yej')
);
