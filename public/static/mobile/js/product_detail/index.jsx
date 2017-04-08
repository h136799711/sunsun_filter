const { Carousel  } = antd;
const Product_detail = React.createClass({
    render(){
        const {data} = this.props;
        let list = data.map((val,index)=>{
            return(
                <div style={{height:'100vw',width:'100vw'}} key={index}><a href={val.href}><img src={val.img} style={{width:'100%',height:'100%'}} /></a></div>
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
    <Product_detail data={product_detail_carousel}/>,
    document.getElementById('product_detail')
);