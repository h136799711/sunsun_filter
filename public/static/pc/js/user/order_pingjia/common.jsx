const { Upload, Button, Icon, message, Spin } = antd;
const imgUrl = 'http://api.moodwo.com/public/index.php/picture/index?id=';

const Pic_upload = React.createClass({
    getInitialState() {
        return {
            fileList: !this.props.default ? [] : [{
                url: imgUrl + this.props.default
            }]
        };
    },
    uploaderChange(info){
        let fileList = info.fileList;
        // 1. 上传列表数量的限制
        //    只显示前5张图片
        fileList = fileList.slice(0,5);

        // 2. 读取远程路径并显示链接
        fileList = fileList.map((file) => {
            if (file.response) {
                // 组件会将 file.url 作为链接进行展示
                file.url = imgUrl+file.response.id;
            }
            return file;
        });

        // 3. 按照服务器返回信息筛选成功上传的文件
        fileList = fileList.filter((file) => {
            if (file.response) {
                return parseInt(file.response.status);
            }
            return true;
        });

        this.setState({ fileList });
        if(this.props.selectOver){
            if(fileList.length!=0){
                let ids = [];
                fileList.forEach(function(val){
                    if(val.response){
                        ids.push(val.response.id);
                    }
                });
                this.props.selectOver(ids);
            }else{
                this.props.selectOver([]);
            }
        }
    },
    render(){
        const props = {
            name: 'image',
            action: getBaseUrl() + '/mengwu/fileupload/index',
            listType: 'picture',
            accept: '.jpe, .jpg, .jpeg, .gif, .png, .bmp',
            defaultFileList: [],
            fileList: this.state.fileList,
            showUploadList: true,
            onPreview: (file) => {
                this.setState({
                    priviewImage: file.url,
                    priviewVisible: true
                });
            },
            onChange: this.uploaderChange
        };
        return(
            <div className="oper">
                <Upload {...props}>
                    <a className="btn up-pic" href="javascript:void(0);">晒照片</a><span className="tip">限5张</span>
                </Upload>
            </div>
        );
    }
});
const Pingjia = React.createClass({
    getInitialState(){
        let data = items.map(function(val){
            return {pid: val.id,psku_id: val.psku_id,group_id:val.group_id,package_id:val.package_id,imgs:[],comment:'好评!'}
        });
        return({
            list: items,
            data,
            loading: false
        });
    },
    selectOver(index,ids){
        let data = this.state.data;
        data[index].imgs = ids;
        this.setState({data});
    },
    changeComment(index,e){
        let data = this.state.data;
        data[index].comment = e.target.value;
        this.setState({data});
    },
    save(){
        let data = this.state.data;
        for(let i=0; i<data.length; i++){
            if($.trim(data[i].comment)==""){
                let refs = 'comment'+data[i].pid;
                this.refs[refs].focus();
                message.error('有商品没有评价哦，请先完成评价后保存~');
                return;
            }
        }
        let url = getBaseUrl() + '/mengwu/mengwuApi/order_comment';

        let pids = data.map(val=>val.pid);
        let imgs = data.map(val=>val.imgs.join('-'));
        let group_ids = data.map(val=>val.group_id);
        let package_ids = data.map(val=>val.package_id);
        let psku_ids = data.map(val=>val.psku_id);

        let formData = {
            pid: pids.join(','),
            order_code: order_code,
            comment: data.map(val=>val.comment),
            attachments: imgs.join(','),
            psku_id: psku_ids.join(','),
            group_id: group_ids.join(','),
            package_id: package_ids.join(',')

        };
        this.setState({loading:true});
        _post(url,jQuery.param(formData),{data_back: function(data){
            if(data.status){
                this.setState({loading:false});
                window.location.replace(getBaseUrl() + '/mengwu/user/order_pingjia_suc');
            }else{
                this.setState({loading:false});
                if(typeof data.info.redirect !="undefined"){
                    message.error(data.info.info);
                    window.location.href = data.info.redirect;
                }else{
                    message.error(data.info);
                }
            }
        }.bind(this)});
    },
    render(){
        let data = this.state.data;
        const list = this.state.list.map((val,index)=>{
            let comment = _.find(data,['pid',val.id]).comment;
            return(
                <div className="item-wrap">
                    <div className="item">
                        <div className="item-pic"><img src={val.img}/></div>
                        <div className="item-name">{val.name}</div>
                    </div>
                    <div className="comment">
                        <textarea ref={'comment'+index} placeholder="亲，写点评价吧，您的评价对其他买家有很大的帮助" value={comment} onChange={e=>this.changeComment(index,e)} />
                        <Pic_upload selectOver={ids=>this.selectOver(index,ids)}/>
                    </div>
                </div>
            )
        });
        return(
            <Spin spinning={this.state.loading}>
                <div className="pingjia-list" id="pingjia">
                    {list}
                </div>
                <div className="save">
                    <a className="btn btn-save" href="javascript:void(0);" onClick={this.save}>保存评价</a>
                </div>
            </Spin>
        );
    }
});
ReactDOM.render(
    <Pingjia />,
    document.getElementById('pingjia')
);