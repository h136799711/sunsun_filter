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

const Red_envelope = React.createClass({
    render(){

        let list = this.props.relist.map(function(d){
            let tip = "";
            switch(d.state){
                case 0:
                    tip = "立即使用";
                    break;
                case 1:
                    tip = "已使用";
                    break;
                case 2:
                    tip = "已过期";
                    break;
            }
            return(
                <li>
                    <div className="re-detail">
                        <div className="re-title"><h4>{d.name}</h4><span>{typeof(d.explain)!='undefined'?'（'+d.explain+'）':''}</span></div>
                        <div className="re-content"><div className="l">{d.money}</div><div className="r"><h3>{d.type}</h3><h5>有效期:{d.deadline}</h5></div></div>
                    </div>
                    {d.state==0?<a href="javascript:void(0);"><div className="re-tip">{tip}</div></a>:<div className="re-tip">{tip}</div>}
                </li>
            );
        });
        return <div className="red-envelope-content"><ul>{list}</ul></div>;
    }
});

// const Redata = [
//     {tab: "未使用" , list:[
//         {state: 0, name: "衣成天品专场红包", explain: "仅限品牌专场使用", money: 30, type: "红包", deadline: "2016-06-01至2016-06-10"},
//         {state: 0, name: "衣成天品专场红包", explain: "仅限品牌专场使用", money: 40, type: "红包", deadline: "2016-06-01至2016-06-10"}
//     ]},
//     {tab: "已使用" , list:[
//         {state: 1, name: "世纪天成专场红包", explain: "仅限品牌专场使用", money: 50, type: "红包", deadline: "2016-04-08至2016-04-10"}
//     ]},
//     {tab: "已过期" , list:[
//         {state: 2, name: "西山居专场红包", explain: "仅限品牌专场使用", money: 100, type: "红包", deadline: "2016-04-08至2016-04-10"}
//     ]}
// ];

const Re_box = React.createClass({
    getInitialState(){
        return({
            redata: this.dealData(data_info)
        });
    },
    dealData(data){
        let redata = [
            {tab: "未使用", list: []},
            {tab: "已使用", list: []},
            {tab: "已过期", list: []}
        ];
        console.log(data);
        data.forEach(function(val){
            let d = {state: 0, name: val.name, money: val.money, type: "红包", deadline: ""};
            let start = parseInt(val.get_time)*1000;
            let end = parseInt(val.expire)*1000;
            d.deadline = this.time2Date(start) + '至' + this.time2Date(end);
            //判断未使用
            if(parseInt(val.use_status)){
                //已使用
                redata[1].list.push(d);
                d.state = 1;
            }else{
                //未使用
                d.state = 0;

                // 判断是否过期
                if(Date.parse(new Date())>end){
                    //已过期
                    d.state = 2;
                    redata[2].list.push(d);
                }else{
                    redata[0].list.push(d);
                }
            }

        }.bind(this));
        return redata;
    },
    time2Date(time){
        let date = new Date(time);
        let m = date.getMonth()+1;
        m = m.length == 1 ? '0' + m : m;
        let d = date.getDate();
        d = d.length == 1 ? '0' + d : d;
        return date.getFullYear()+'-'+m+'-'+d;
    },
    render(){
        let tabpanes = this.state.redata.map(function(val){
            return(
                <Tabpane tab={val.tab}>
                    <Red_envelope relist={val.list} />
                </Tabpane>
            );
        });
        return(
            <Tabs>
                {tabpanes}
            </Tabs>
        );
    }
});

const Re_tabs = document.getElementById('re_tabs');
if(Re_tabs!==null){
    ReactDOM.render(
        <Re_box />,
        Re_tabs
    );
}

// const Ccdata = [
//     {tab: "未使用" , list:[
//         {state: 0, name: "满200元使用", money: 30, type: "优惠券", deadline: "2016-06-01至2016-06-10"},
//         {state: 0, name: "满100元使用", money: 40, type: "优惠券", deadline: "2016-06-01至2016-06-10"}
//     ]},
//     {tab: "已使用" , list:[
//         {state: 1, name: "满50元使用", money: 50, type: "优惠券", deadline: "2016-04-08至2016-04-10"}
//     ]},
//     {tab: "已过期" , list:[
//         {state: 2, name: "满300元使用", money: 100, type: "优惠券", deadline: "2016-04-08至2016-04-10"}
//     ]}
// ];

const Cc_tabs = document.getElementById('cc_tabs');
if(Cc_tabs!==null){
    ReactDOM.render(
        <Re_box />,
        Cc_tabs
    );
}

