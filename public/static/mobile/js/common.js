"use strict";

var _antd = antd,
    Icon = _antd.Icon;


var Test = React.createClass({
    displayName: "Test",
    render: function render() {
        return React.createElement(Icon, { type: "plus-circle-o" });
    }
});

ReactDOM.render(React.createElement(Test, null), document.getElementById('plus-circle-o'));

var Right = React.createClass({
    displayName: "Right",
    render: function render() {
        return React.createElement(Icon, { type: "right" });
    }
});

ReactDOM.render(React.createElement(Right, null), document.getElementById('right'));

//# sourceMappingURL=common.js.map