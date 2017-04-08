const { Carousel  } = antd;

const Test = React.createClass({
    render(){
        const {data} = this.props;
        let list = data.map((val,index)=>{
            return(
                <div style={{height:'40vw',width:'70vw'}} key={index}><a href={val.href}><img src={val.img} style={{width:'100%',height:'100%',borderRadius:10}} /></a></div>
            )
        });
        return(
            <Carousel autoplay>
                {list}
            </Carousel>
        );
    }
});

ReactDOM.render(
    <Test data={index_carousel}/>,
    document.getElementById('app')
);