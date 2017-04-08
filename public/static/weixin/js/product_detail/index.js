'use strict';

var _antd = antd,
    Carousel = _antd.Carousel;

var Product_detail = React.createClass({
    displayName: 'Product_detail',
    render: function render() {
        var data = this.props.data;

        var list = data.map(function (val, index) {
            return React.createElement(
                'div',
                { style: { height: '100vw', width: '100vw' }, key: index },
                React.createElement(
                    'a',
                    { href: val.href },
                    React.createElement('img', { src: val.img, style: { width: '100%', height: '100%' } })
                )
            );
        });
        return React.createElement(
            Carousel,
            { autoplay: true },
            list
        );
    }
});

ReactDOM.render(React.createElement(Product_detail, { data: product_detail_carousel }), document.getElementById('product_detail'));

//# sourceMappingURL=index.js.map