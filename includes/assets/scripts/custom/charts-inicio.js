var Charts = function () {

    return {
        //main function to initiate the module

        init: function () {

            App.addResponsiveHandler(function () {
                 Charts.initPieCharts(); 
            });
            
        },
        initPieCharts: function () {

            
            // GRAPH 1
            if(dataEmpleadosVacaciones.length == 0){
                dataEmpleadosVacaciones.push({
                    label:'Sin Datos',
                    data:0
                })
            }
                $.plot($("#pie_chart_1"), dataEmpleadosVacaciones, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 1,
                                formatter: function (label, series) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+ formatTotal(series.data[0][1],false,false) + '<br/>' + Math.round(series.percent) + '%</div>';
                                },
                                background: {
                                    opacity: 0.8
                                }
                            }
                        }
                    },
                    legend: {
                        show: true
                    }
                });
             
            
            // GRAPH 2
            /*if(dataEmpleadosVacaciones.length == 0){
                dataEmpleadosVacaciones.push({
                    label:'Sin Datos',
                    data:0
                })
            }
            
            $.plot($("#pie_chart_2"), dataEmpleadosVacaciones, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 1,
                                formatter: function (label, series) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
                                },
                                background: {
                                    opacity: 0.8
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
             });*/
             
            
            // GRAPH 3
            /*if(dataProveedores.length == 0){
                dataProveedores.push({
                    label:'Sin Datos',
                    data:0
                })
            }
            if(nivel_usuario <= nivel_operacion_ver_dash_compras){
                $.plot($("#pie_chart_3"), dataProveedores, {
                        series: {
                            pie: {
                                show: true,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 1,
                                    formatter: function (label, series) {
                                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
                                    },
                                    background: {
                                        opacity: 0.8
                                    }
                                }
                            }
                        },
                        legend: {
                            show: true
                        }
                    });
            }*/
        },

        initBarCharts: function () {

            // bar chart:
            /*var data = GenerateSeries(0);
     
            function GenerateSeries(added){
                var data = [];
                var start = 100 + added;
                var end = 200 + added;
         
                for(i=1;i<=20;i++){        
                    var d = Math.floor(Math.random() * (end - start + 1) + start);        
                    data.push([i, d]);
                    start++;
                    end++;
                }
         
                return data;
            }*/
         
            var options = {
                    series:{
                        bars:{show: true}
                    },
                    bars:{
                                    barWidth: 0.8,
                                    lineWidth: 0, // in pixels
                                    shadowSize: 0,
                                    align: 'left'
                    },            

                    grid:{
                         tickColor: "#eee",
                                borderColor: "#eee",
                                borderWidth: 1
                    }
            };
 
            $.plot($("#pie_chart_2"),
             [{
                data: dataEmpleadosVacaciones,//data,
                lines: {
                    lineWidth: 1,
                },
                shadowSize: 0
             }]
             , options);

            // horizontal bar chart:

            /*var data1 = [
                [10, 10], [20, 20], [30, 30], [40, 40], [50, 50]
            ];
         
            var options = {
                    series:{
                        bars:{show: true}
                    },
                    bars:{
                        horizontal:true,
                        barWidth:6,
                                    lineWidth: 0, // in pixels
                                    shadowSize: 0,
                                    align: 'left'
                    },
                    grid:{
                         tickColor: "#eee",
                                borderColor: "#eee",
                                borderWidth: 1
                    }
            };*/
         
            //$.plot($("#chart_1_2"), [data1], options);  
        }
        
    };

}();