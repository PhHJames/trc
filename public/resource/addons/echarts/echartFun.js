function creationEchart (id,title,colorObj,chartTxt,dataNum) {
    var myChart = echarts.init(document.getElementById(id));

    var option = {
        title : {
            text: title,
            left: 20,  
            top:10,
        },
        grid:{
            left: 20,  
            right: 50,
            top:50,
            bottom:20,    
            containLabel: true  
        },
        tooltip : {
            trigger: 'axis'
        },
        calculable : true,
        axisLabel: {
            formatter : function(value, index){
                if(value>10000 && value<100000000){
                    return value/10000 +'万';
                } else if(value>100000000){
                    return value/100000000 +'亿';
                }
                return value;
            }
        },
        xAxis : [
            {
                type : 'value',
                boundaryGap : [0, 0.01],
                axisLine: {
                    lineStyle: {
                        color: '#3c8dbc', // 颜色
                        width: 2 // 粗细
                    }
                },
                axisLabel: {
                    show: true,
                     textStyle: {
                       color: '#000', 
                     }
                },
            }
        ],
        yAxis : [
            {
                type : 'category',
                data : chartTxt,
                axisLine: {
                    lineStyle: {
                        color: '#3c8dbc', // 颜色
                        width: 2 // 粗细
                    }
                },
                axisLabel: {
                    show: true,
                    textStyle: {
                    color: '#000', 
                    },
                    width:200
                },
            }
        ],
        series : [
            {
                type:'bar',
                barWidth:'30%',
                itemStyle: {
                    normal: {
                        barBorderRadius: [0, 10, 10, 0],
                        color: function(params) {
                            var colorList = colorObj;
                            return colorList[params.dataIndex]
                        },
                        label : {
                            show: true,
                            position: 'right',
                            // formatter : function(params){
                            //     if(params.value>10000 && params.value<100000000){
                            //         return params.value/10000 +'万';
                            //     } else if(params.value>100000000){
                            //         return params.value/100000000 +'亿';
                            //     }
                            //     return params.value;
                            // }
                        }
                    }
                },
                data:dataNum,
            },
        ]
    };
                        
     // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);                   
}
