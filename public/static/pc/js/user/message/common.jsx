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

const Comment_item = React.createClass({
    getDate(time){
        let _time = new Date(time*1000);
        let day = _time.getDate();
        day = day<10 ? '0' + day : day;
        let month = _time.getMonth()+1;
        month = month<10 ? '0' + month : month;
        let m = _time.getMinutes();
        m = m<10 ? '0' + m : m;
        let h = _time.getHours();
        h = m<10 ? 'h' + h : m;
        _time = _time.getFullYear()+'-'+month+'-'+day +' '+h+':'+m;
        return _time;
    },
    render(){
        const data = this.props.data;
        let type = data.fid==7?'share':'question';
        let url = getBaseUrl() + '/mengwu/yuerjing/detail/type/' + type + '/tid/' + data.tid;
        if(this.props.type == 'receive'){
            return(
                <div className="list-item">
                    <div className="item-top">
                        <div className="head">
                            <img src={data.author_info.head} />
                        </div>
                        <div className="top-content">
                            <div className="name">{data.author_info.nickname}</div>
                            <div className="text">{data.comment}</div>
                        </div>
                        <a className="delete hide" href="javascript:void(0);">删除</a>
                    </div>
                    <div className="rf"><em>{data.to_uid=='0'?'评论我的育儿经：':'评论我的回复：'}</em><span>{data.re_comment}</span></div>
                    <div className="time">{this.getDate(data.dateline)}<span>来自育儿经</span></div>
                    <div className="reply-wrap">
                        <div className="btn-reply"><a href={url} target="_blank">回复</a></div>
                    </div>
                </div>
            );
        }else{
            return(
                <div className="list-item">
                    <div className="item-top">
                        <div className="head">
                            <img src={data.author_info.head} />
                        </div>
                        <div className="top-content">
                            <div className="name">{data.author_info.nickname}</div>
                            <div className="text">{data.comment}</div>
                        </div>
                        <a className="delete hide" href="javascript:void(0);">删除</a>
                    </div>
                    <div className="rf"><em>{data.to_uid=='0'?'评论育儿经：':'评论回复：'}</em><span>{data.re_comment}</span></div>
                    <div className="time">{this.getDate(data.dateline)}<span>来自育儿经</span></div>
                    <div className="reply-wrap">
                        <div className="btn-reply"><a href={url} target="_blank">查看</a></div>
                    </div>
                </div>
            );
        }

    }
});

const Comment_list = React.createClass({
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
        let url = getBaseUrl() + '/mengwu/user/message/t/comment';
        let data = {
            page_no: page_no,
            page_size: this.state.page_size
        };
        if(this.props.type=='receive'){
            data.type = 'receive';
        }else{
            data.type = 'send'
        }

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
                <Comment_item data={val} {...this.props}/>
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
                list = <div className="text-center" style={{marginTop:50}}>没有收到任何回复哦~</div>
            }

        }
        return(
            <Spin spinning={this.state.loading}>
                <div className="cm-list">
                    {list}
                    {page}
                </div>
            </Spin>
        );
    }
});

const Comment = React.createClass({
    getDefaultProps(){
        return({data:[]});
    },
    render(){
        return(
            <Tabs>
                <Tabpane tab="收到的评论">
                    <Comment_list type="receive" />
                </Tabpane>
                <Tabpane tab="发出的评论">
                    <Comment_list type="send" />
                </Tabpane>
            </Tabs>
        );
    }
});
const c_comment = document.getElementById('comment');
if(c_comment!==null){
    ReactDOM.render(
        <Comment />,
        c_comment
    );
}


const Zan_item = React.createClass({
    render(){
        const data = this.props.data;
        let like_time = new Date(data.like_time*1000);
        let day = like_time.getDate();
        day = day<10 ? '0' + day : day;
        let month = like_time.getMonth()+1;
        month = month<10 ? '0' + month : month;
        let m = like_time.getMinutes();
        m = m<10 ? '0' + m : m;
        let h = like_time.getHours();
        h = m<10 ? 'h' + h : m;
        like_time = like_time.getFullYear()+'-'+month+'-'+day +' '+h+':'+m;
        return(
            <div className="list-item">
                <div className="item-top">
                    <div className="head">
                        <img src={data.head} />
                    </div>
                    <div className="top-content">
                        <div className="name">{data.nickname}</div>
                        <div className="text">赞了我的育儿经</div>
                    </div>
                    <a className="delete hide" href="javascript:void(0);">删除</a>
                </div>
                <div className="rf"><em className="pink">@我：</em><span>{data.message}</span></div>
                <div className="time">{like_time}<span>来自育儿经</span></div>
            </div>
        );
    }
});

const Zan_list = React.createClass({
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
        let url = getBaseUrl() + '/mengwu/user/message/t/zan';
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
                <Zan_item data={val}/>
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
                list = <div className="text-center" style={{marginTop:50}}>没有收到任何点赞哦~</div>
            }

        }
        return(
            <Spin spinning={this.state.loading}>
                <div className="cm-list">
                    {list}
                    {page}
                </div>
            </Spin>
        );
    }
});

const Zan = React.createClass({
    render(){
        return(
            <Zan_list />
        );
    }
});

const c_zan = document.getElementById('zan');
if(c_zan!=null){
    ReactDOM.render(
        <Zan />,
        c_zan
    );
}
