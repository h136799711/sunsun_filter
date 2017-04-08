'use strict';

var _antd = antd;
var Upload = _antd.Upload;
var Button = _antd.Button;
var Icon = _antd.Icon;
var message = _antd.message;
var Spin = _antd.Spin;

var imgUrl = 'http://api.moodwo.com/public/index.php/picture/index?id=';

var Pic_upload = React.createClass({
    displayName: 'Pic_upload',
    getInitialState: function getInitialState() {
        return {
            fileList: !this.props.default ? [] : [{
                url: imgUrl + this.props.default
            }]
        };
    },
    uploaderChange: function uploaderChange(info) {
        var _this = this;

        var fileList = info.fileList;
        // 1. 上传列表数量的限制
        //    只显示前5张图片
        fileList = fileList.slice(0, 5);

        // 2. 读取远程路径并显示链接
        fileList = fileList.map(function (file) {
            if (file.response) {
                // 组件会将 file.url 作为链接进行展示
                file.url = imgUrl + file.response.id;
            }
            return file;
        });

        // 3. 按照服务器返回信息筛选成功上传的文件
        fileList = fileList.filter(function (file) {
            if (file.response) {
                return parseInt(file.response.status);
            }
            return true;
        });

        this.setState({ fileList: fileList });
        if (this.props.selectOver) {
            if (fileList.length != 0) {
                (function () {
                    var ids = [];
                    fileList.forEach(function (val) {
                        if (val.response) {
                            ids.push(val.response.id);
                        }
                    });
                    _this.props.selectOver(ids);
                })();
            } else {
                this.props.selectOver([]);
            }
        }
    },
    render: function render() {
        var _this2 = this;

        var props = {
            name: 'image',
            action: getBaseUrl() + '/mengwu/fileupload/index',
            listType: 'picture',
            accept: '.jpe, .jpg, .jpeg, .gif, .png, .bmp',
            defaultFileList: [],
            fileList: this.state.fileList,
            showUploadList: true,
            onPreview: function onPreview(file) {
                _this2.setState({
                    priviewImage: file.url,
                    priviewVisible: true
                });
            },
            onChange: this.uploaderChange
        };
        return React.createElement(
            'div',
            { className: 'oper' },
            React.createElement(
                Upload,
                props,
                React.createElement(
                    'a',
                    { className: 'btn up-pic', href: 'javascript:void(0);' },
                    '晒照片'
                ),
                React.createElement(
                    'span',
                    { className: 'tip' },
                    '限5张'
                )
            )
        );
    }
});
var Pingjia = React.createClass({
    displayName: 'Pingjia',
    getInitialState: function getInitialState() {
        var data = items.map(function (val) {
            return { pid: val.id, psku_id: val.psku_id, group_id: val.group_id, package_id: val.package_id, imgs: [], comment: '好评!' };
        });
        return {
            list: items,
            data: data,
            loading: false
        };
    },
    selectOver: function selectOver(index, ids) {
        var data = this.state.data;
        data[index].imgs = ids;
        this.setState({ data: data });
    },
    changeComment: function changeComment(index, e) {
        var data = this.state.data;
        data[index].comment = e.target.value;
        this.setState({ data: data });
    },
    save: function save() {
        var data = this.state.data;
        for (var i = 0; i < data.length; i++) {
            if ($.trim(data[i].comment) == "") {
                var refs = 'comment' + data[i].pid;
                this.refs[refs].focus();
                message.error('有商品没有评价哦，请先完成评价后保存~');
                return;
            }
        }
        var url = getBaseUrl() + '/mengwu/mengwuApi/order_comment';

        var pids = data.map(function (val) {
            return val.pid;
        });
        var imgs = data.map(function (val) {
            return val.imgs.join('-');
        });
        var group_ids = data.map(function (val) {
            return val.group_id;
        });
        var package_ids = data.map(function (val) {
            return val.package_id;
        });
        var psku_ids = data.map(function (val) {
            return val.psku_id;
        });

        var formData = {
            pid: pids.join(','),
            order_code: order_code,
            comment: data.map(function (val) {
                return val.comment;
            }),
            attachments: imgs.join(','),
            psku_id: psku_ids.join(','),
            group_id: group_ids.join(','),
            package_id: package_ids.join(',')

        };
        this.setState({ loading: true });
        _post(url, jQuery.param(formData), { data_back: function (data) {
                if (data.status) {
                    this.setState({ loading: false });
                    window.location.replace(getBaseUrl() + '/mengwu/user/order_pingjia_suc');
                } else {
                    this.setState({ loading: false });
                    if (typeof data.info.redirect != "undefined") {
                        message.error(data.info.info);
                        window.location.href = data.info.redirect;
                    } else {
                        message.error(data.info);
                    }
                }
            }.bind(this) });
    },
    render: function render() {
        var _this3 = this;

        var data = this.state.data;
        var list = this.state.list.map(function (val, index) {
            var comment = _.find(data, ['pid', val.id]).comment;
            return React.createElement(
                'div',
                { className: 'item-wrap' },
                React.createElement(
                    'div',
                    { className: 'item' },
                    React.createElement(
                        'div',
                        { className: 'item-pic' },
                        React.createElement('img', { src: val.img })
                    ),
                    React.createElement(
                        'div',
                        { className: 'item-name' },
                        val.name
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'comment' },
                    React.createElement('textarea', { ref: 'comment' + index, placeholder: '亲，写点评价吧，您的评价对其他买家有很大的帮助', value: comment, onChange: function onChange(e) {
                            return _this3.changeComment(index, e);
                        } }),
                    React.createElement(Pic_upload, { selectOver: function selectOver(ids) {
                            return _this3.selectOver(index, ids);
                        } })
                )
            );
        });
        return React.createElement(
            Spin,
            { spinning: this.state.loading },
            React.createElement(
                'div',
                { className: 'pingjia-list', id: 'pingjia' },
                list
            ),
            React.createElement(
                'div',
                { className: 'save' },
                React.createElement(
                    'a',
                    { className: 'btn btn-save', href: 'javascript:void(0);', onClick: this.save },
                    '保存评价'
                )
            )
        );
    }
});
ReactDOM.render(React.createElement(Pingjia, null), document.getElementById('pingjia'));

//# sourceMappingURL=common.js.map