'use strict';

var _antd = antd,
    Carousel = _antd.Carousel;


var Test = React.createClass({
    displayName: 'Test',
    render: function render() {
        var data = this.props.data;

        var list = data.map(function (val, index) {
            return React.createElement(
                'div',
                { style: { height: '40vw', width: '70vw' }, key: index },
                React.createElement(
                    'a',
                    { href: val.href },
                    React.createElement('img', { src: val.img, style: { width: '100%', height: '100%', borderRadius: 10 } })
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

ReactDOM.render(React.createElement(Test, { data: index_carousel }), document.getElementById('app'));

//# sourceMappingURL=common.js.map