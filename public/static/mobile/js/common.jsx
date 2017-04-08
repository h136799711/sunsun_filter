const { Icon  } = antd;

const Test = React.createClass({
    render(){
        return(
            <Icon type="plus-circle-o" />
        );
    }
});

ReactDOM.render(
    <Test/>,
    document.getElementById('plus-circle-o')
);

const Right = React.createClass({
    render(){
        return(
            <Icon type="right" />
        );
    }
});

ReactDOM.render(
    <Right/>,
    document.getElementById('right')
);
