
MC.last_tab = '#report-nps-tab';
MC.chart = {};
MC.stylist_id = '';

MC.get_nps_transition = function(questionnaire_id) {
    //$('.nps-chart > .nps').hide();
    //var term = $('#select-nps-term').val() + 'days';
    MC.ajax_get(
        //'/ajax/nps/transition/'+questionnaire_id+'/'+term+'/'+MC.stylist_id,
        '/ajax/nps/transition/'+questionnaire_id+'/'+MC.stylist_id,
        function(response) {
            
            console.log(response.data);

            var i = 0;
            var _nps_transitions = [];
            var nps_transitions = {};
            var line_data = [];
            var bar_data = [];
            var max_question = [];
            var max_nps = [];
            var labels = [];
            $.each(response.data.nps_transition, function(month, nps_transition) {
                //console.log(nps_transition);
                var since = moment(nps_transition.since).format('M/D');
                var until = moment(nps_transition.until).format('M/D');
                /*
                if (i === 0) {
                    $('#term-label').text(since+'～'+until);

                    data = [
                        nps_transition.promoter,
                        nps_transition.passive,
                        nps_transition.detractor
                    ];
                    MC.view_nps_chart($('#nps-doughnut-chart'), data);

                    var text_color = 'text-warning';
                    if (nps_transition.promoter > 0 || nps_transition.passive > 0 || nps_transition.detractor > 0) {
                        if (nps_transition.nps > 15) {
                            text_color = 'text-success';
                        } else if (nps_transition.nps < -15) {
                            text_color = 'text-danger';
                        }
                    }
                    $('#nps-doughnut-chart + .nps > span').text(nps_transition.nps).addClass(text_color);
                    $('#nps-doughnut-chart').next('.nps').show();
                }
                */
                bar_data.unshift(nps_transition.promoter);
                line_data.unshift(nps_transition.nps);
                max_question.unshift(nps_transition.max_question);
                max_nps.unshift(nps_transition.max_nps);
                labels.unshift(nps_transition.month);

                _nps_transitions.push({key: since+'～'+until, val:  nps_transition.nps});

                i++;
            });

            $.each(_nps_transitions.reverse(), function(i, values) {
                nps_transitions[values.key] = values.val;
            });

            if (Object.keys(nps_transitions).length > 0) {
                var $canvas = $('#nps-line-chart');
                //console.log(nps_transitions);
                //MC.view_line_chart($canvas, nps_transitions, -100);
                //console.log(line_data);
                MC.view_fan_chart($canvas, line_data, bar_data, labels, max_question, max_nps);
            }
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.get_sub_question_answers = function(questionnaire_id, term) {
    $('.sub-question .carousel-item').remove();
    $('.sub-question').each(function() {
        var question_id = $(this).data('question-id');
        MC.ajax_get(
            '/ajax/report/sub_question_answer/'+questionnaire_id+'/'+question_id+'/'+term+'/'+MC.stylist_id,
            function(response) {
                var sub_answers = response.data;
                $.each(sub_answers, function(i, sub_answer) {
                    var $item = $('#carousel-inner-dummy > .carousel-item').clone(false);
                    $item.removeAttr('id');
                    if (i === 0) {
                        $item.addClass('active').data('interval', 2000 * sub_answer.sub_answer.length);
                    }
                    $item.children('pre').text(sub_answer.sub_answer);
                    $item.children('.answer-date').text(moment(sub_answer.created_at).format('YYYY/MM/DD HH:mm'));
                    $('#sub-question-'+question_id+' > .carousel-inner').append($item);
                });
            },
            function(response) {
            },
            function() {
            }
        );
    });
};

MC.get_average_levels = function(questionnaire_id, term) {
    $('#nps-table .correlation').removeClass('light-green');
    $('#nps-table .correlation').removeClass('orange');
    $('#nps-table .correlation').removeClass('lighten-1');
    $('#nps-table .correlation').removeClass('lighten-3');
    $('#nps-table .correlation').removeClass('lighten-5');
    $('#nps-table .correlation, #nps-table .average').text('0');
    $('canvas[id^=line-chart-question-]').remove();
    MC.ajax_get(
        '/ajax/report/average_level/'+questionnaire_id+'/'+term+'/'+MC.stylist_id,
        function(response) {

            console.log(response.data);

            var chart_data = [];
            var average_levels = response.data.average_levels;
            $.each(average_levels, function(question_number, values) {
                if (values) {
                    var $canvas = $('<canvas></canvas>', {
                        id: 'line-chart-question-'+question_number
                    }).data('question-number', question_number);
                    $('#question-collapse-'+question_number+' .average-transition-chart').append($canvas);
                    MC.view_line_chart($canvas, values);
                }
            });
            var nps_levels = response.data.nps_levels;

            $('#nps-result > .result-err-alert').hide();
            $('#nps-result > .result-alert').hide();
            $('#nps-result > .not-recomend-err-alert').hide();

            if (Object.keys(nps_levels).length > 0) 
            {
                var nps_count = response.data.nps_count;
                var max_nps = response.data.max_nps;
                console.log(max_nps)

                if (max_nps) {
                    $('.question-item').text(max_nps.item);
                    // $('.question-item').text("test");
                    $('#nps-result > .result-alert').show();
                } else if (nps_count < 50) {
                    $('#nps-result > .result-err-alert').show();
                } else {
                    $('#nps-result > .not-recomend-err-alert').show();
                }
                $('#nps-result').show();
                var $canvas = $('#nps-scatter-chart');
                MC.view_scatter_chart($canvas, nps_levels);
            } 
            else 
            {
                $('#nps-result').hide();
            }
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.get_nps_levels = function(questionnaire_id) {
    MC.ajax_get(
        '/ajax/report/nps_level/'+questionnaire_id+'/'+MC.stylist_id,
        function(response) {
            var nps_levels = response.data.nps_levels;
            //console.log(nps_levels);
            if (nps_levels && Object.keys(nps_levels).length > 0) 
            {
                var $canvas = $('#nps-current-scatter-chart');
                MC.view_scatter_chart($canvas, nps_levels);
            } 
            else if (typeof MC.chart['nps-current-scatter-chart'] != 'undefined') 
            {
                MC.chart['nps-current-scatter-chart'].destroy();
            }
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.get_count_answers = function(questionnaire_id, term) {
    $('canvas[id^=level-chart-question-]').remove();
    MC.ajax_get(
        '/ajax/report/count_answers/'+questionnaire_id+'/'+term+'/'+MC.stylist_id,
        function(response) {
            //console.log(response.data);
            var levels = response.data.count_levels;
            $.each(levels, function(question_number, values) {
                if (values) 
                {
                    //console.log(values);
                    var $canvas = $('<canvas></canvas>', {
                        id: 'level-chart-question-'+question_number
                    }).data('question-number', question_number);
                    $('#question-collapse-'+question_number+' .count-answer-chart').prepend($canvas);
                    // MC.view_bar_chart($canvas, values);
                    MC.view_pie_chart($canvas, values, true, null);
                }
            });
            $('canvas[id^=pie-chart-question-]').remove();
            var select_ones = response.data.count_select_ones;
            var colors = response.data.color_select_ones;
            //console.log(select_ones);
            $.each(select_ones, function(question_number, values) {
                if (values) 
                {
                    var $canvas = $('<canvas></canvas>', {
                        id: 'pie-chart-question-'+question_number
                    }).data('question-number', question_number);
                    $('#question-collapse-'+question_number).prepend($canvas);
                    var bg_colors = null;
                    if (typeof colors[question_number] !== 'undefined') {
                        bg_colors = colors[question_number];
                    }
                    MC.view_pie_chart($canvas, values, false, bg_colors);
                }
            });
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.view_scatter_chart = function($canvas, nps_levels) {
    if (typeof MC.chart[$canvas.attr('id')] != 'undefined') {
        MC.chart[$canvas.attr('id')].destroy();
    }
    // console.log(nps_levels);
    var chart_data = [];
    var base_yaxes = 3;
    var y_sum = 0;
    var y_cnt = 0;
    var x_max = 1.0;
    var _x_max = -1.0;
    var x_min = -1.0;
    var _x_min = 1.0;
    var y_max = 5.0;
    var _y_max = 1.0;
    var y_min = 1.0;
    var _y_min = 5.0;
    $.each(nps_levels, function(i, nps) {
        if (nps.nps_flag === null) {
            var x = parseFloat(nps.correlation);
            var y = parseFloat(nps.average);
            y_sum += y;
            y_cnt++;
            chart_data.push({
                x: x,
                y: y,
                item: nps.item + '(Q'+i+')'
            });
            if (x > _x_max) _x_max = x;
            if (y > _y_max) _y_max = y;
            if (x < _x_min) _x_min = x;
            if (y < _y_min) _y_min = y;
        }
    });
    _x_max = (_x_max - (_x_max % 0.1));
    _x_min = (_x_min - (_x_min % 0.1));
    _y_max = (_y_max - (_y_max % 0.2));
    _y_min = (_y_min + (_y_min % 0.2));
    _x_max = (_x_max + 0.1);
    _x_min = (_x_min - 0.1);
    _y_max = (_y_max + 0.2);
    _y_min = (_y_min - 0.2);
    if (_x_max > x_max) _x_max = x_max;
    if (_x_min < x_min) _x_min = x_min;
    if (_y_max > y_max) _y_max = y_max;
    if (_y_min < y_min) _y_min = y_min;
    if (_x_max < 0.1) _x_max = 0.1;
    if (_x_min > -0.1) _x_min = -0.1;
    if (_y_max < 3.2) _y_max = 3.2;
    if (_y_min > 2.8) _y_min = 2.8;
    var scales = {
        xAxes: {min: _x_min, max: _x_max},
        yAxes: {min: _y_min, max: _y_max}
    };

    base_yaxes = y_sum / y_cnt;
    var ctx = $canvas[0].getContext('2d');
    var chart_option = {
        plugins: [ChartDataLabels],
        type: 'scatter',
        data: 
        {
            datasets: 
            [
                // 全体
                {
                    data: chart_data,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    datalabels: 
                    {
                        align: 'right',
                        color: '#fff',
                        font: {size: 14},
                        font :{weight: 900},
                        showSeriesName : true,
                        formatter: function(value, context) 
                        {
                            //console.log(value);
                            return value.item;
                            // return value.question+value.item;
                        }
                    }
                }, 

                // 1番濃い
                {
                    type: 'line',
                    // label: 'コア受け',
                    data: [{x: 0.0, y: base_yaxes}, {x: 1.0, y: base_yaxes}],
                    // backgroundColor: 'rgba(229, 70, 45, 0.7)',
                    backgroundColor : '#ff5722',
                    pointRadius: 0,
                    showLine: true,
                    lineTension: 0,
                    // borderWidth: 0,
                    datalabels: {
                        display: false
                    }
                }, 

                // 2番目に濃い
                {
                    type: 'line',
                    // label: 'コア受け',
                    data: [{x: 0.0, y: 5.0}, {x: 1.0, y: 5.0}],
                    // backgroundColor: 'rgba(255, 255, 255, 0.2)',
                    backgroundColor : 'red',
                    pointRadius: 0,
                    showLine: true,
                    lineTension: 0,
                    // borderColor: ['rgba(229, 70, 45, 0.5)'],
                    // borderWidth: 2,
                    datalabels: {
                        display: false
                    }
                }, 

                // 4番目に濃い
                {
                    type: 'line',
                    // label: 'コア受け',
                    data: [{x: -1.0, y: base_yaxes}, {x: 0.0, y: base_yaxes}],
                    // backgroundColor: 'rgba(255, 255, 255, 0.5)',
                    backgroundColor : 'yellow',
                    pointRadius: 0,
                    showLine: true,
                    lineTension: 0,
                    // borderColor: ['rgba(229, 70, 45, 0.5)'],
                    // borderWidth: 2,
                    datalabels: {
                        display: false
                    }   
                }, 

                // 3番目に濃い
                {
                    type: 'line',
                    // label: '万人受け',
                    data: [{x: -1.0, y: 5.0}, {x: 0.0, y: 5.0}],
                    backgroundColor : '#ff9100',
                    pointRadius: 0,
                    showLine: true,
                    lineTension: 0,
                    // backgroundColor: 'rgba(229, 70, 45, 0.25)',
                    // borderWidth: 0,
                    datalabels: {
                        display: false
                    }
                }
            ]
        },
        
        options: {
            // legend: {
            //     display: false
            // },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    filter: function(item, chart) {
                        return item.text == '顧客愛着率';
                    }
                }
            },
            layout: {
                padding: {right: 100}
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        zeroLineColor: 'rgba(229, 70, 45, 1)',
                        zeroLineWidth: 2,
                        color: 'rgba(255, 255, 255, 0)',
                        lineWidth: 0
                    },
                    //ticks: { min: -1.0, max: 1.0, stepSize: 0.2}
                    ticks: { min: scales.xAxes.min, max: scales.xAxes.max, stepSize: 0.1}
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    //ticks: { min: 1.0, max: 5.0, stepSize: 0.2}
                    ticks: { min: scales.yAxes.min, max: scales.yAxes.max, stepSize: 0.2}
                }]
            },
            tooltips: {
                enabled: false
            }
        }
    };
    var chart = new Chart(ctx, chart_option);
    MC.chart[$canvas.attr('id')] = chart;
};

MC.view_fan_chart = function($canvas, line_data, bar_data, labels, max_question, max_nps) {
    if (typeof MC.chart[$canvas.attr('id')] != 'undefined') {
        MC.chart[$canvas.attr('id')].destroy();
    }
    //console.log(bar_data);
    //console.log(max_nps);
    var ctx = $canvas[0].getContext('2d');
    var chart_option = {
        plugins: [ChartDataLabels],
        type: 'bar',
        data: {
            datasets: [{
                type: 'line',
                label: 'ファン度',
                // labels : ["test"],
                data: line_data,
                borderColor: ['rgba(251, 176, 60, .7)'],
                pointBackgroundColor: [
                    'rgba(240, 117, 29, 1)',
                    'rgba(50, 107, 150, 1)',
                    'rgba(84, 150, 68, 1)',
                    'rgba(232, 59, 29, 1)',
                    'rgba(245, 102, 13, 1)',
                    'rgba(248, 218, 82, 1)'
                ],
                pointBorderColor: [
                    'rgba(240, 117, 29, 1)',
                    'rgba(50, 107, 150, 1)',
                    'rgba(84, 150, 68, 1)',
                    'rgba(232, 59, 29, 1)',
                    'rgba(245, 102, 13, 1)',
                    'rgba(248, 218, 82, 1)'
                ],
                backgroundColor: ['transparent'],
                pointHoverBorderColor: 'rgba(251, 176, 60, 1)',
                pointHoverBackgroundColor: 'rgba(251, 176, 60, 1)',
                borderWidth: 2,
                lineTension: 0.4,
                yAxisID: 'line',
                datalabels: {
                    display: false
                }
            }, {
                type: 'bar',
                label: '顧客愛着率',
                data: bar_data,
                // backgroundColor: 'rgba(251, 176, 60, 1)',
                backgroundColor: [
                    'rgba(240, 117, 29, 1)',
                    'rgba(50, 107, 150, 1)',
                    'rgba(84, 150, 68, 1)',
                    'rgba(232, 59, 29, 1)',
                    'rgba(245, 102, 13, 1)',
                    'rgba(248, 218, 82, 1)'
                ],
                // borderColor: 'rgba(237, 99, 147, 1)',
                yAxisID: 'bar',
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    offset: -6,
                    color: '#090909'
                }
            }],
            labels: labels
        },
        options: {
            scales: {
                yAxes: [{
                    id: 'line',
                    ticks: { min: -100, max: 100, stepSize: 20}
                }, {
                    id: 'bar',
                    ticks: { min: 0, max: 200, stepSize: 20},
                    position: 'right',
                    display: false
                }],
                xAxes: [{
                    barPercentage: 0.4
                }]
            },
            legend: {
                // display: true,
                position: 'bottom',
                // labels: {
                //     filter: function(item, chart) {
                //         return item.text == '顧客愛着率';
                //     }
                // }
            },
            tooltips: {
                enabled: false,
                callbacks: {
                    label: function(item) {
                        var dataIndex = item.datasetIndex;
                        var index = item.index;
                        //console.log(max_question);
                        //console.log(max_nps);
                        if (dataIndex === 0 && max_question[index]) {
                            if (max_nps[index]) {
                                return max_nps[index].item + ',' + max_question[index].item;
                            } else {
                                return ',';
                            }
                        } else {
                            return '';
                        }
                    }
                },
                custom: function(tooltipModel) {
                    var $tooltip = $('#chartjs-tooltip');
                    if ($tooltip.length === 0) {
                        $tooltip = $('<div id="chartjs-tooltip" class="text-center grey lighten-3 shadow"></div>');
                        $('body').append($tooltip);
                    } else {
                        $tooltip.html('');
                    }
                    if (tooltipModel.opacity === 0) {
                        $tooltip.css('opacity', '0');
                        return;
                    }
                    $tooltip.removeClass('above below no-transform');
                    if (tooltipModel.yAlign) {
                        $tooltip.addClass(tooltipModel.yAlign);
                    } else {
                        $tooltip.addClass('no-transform');
                    }
                    //console.log(tooltipModel.body);

                    var position = this._chart.canvas.getBoundingClientRect();

                    if (tooltipModel.body.length > 0&& tooltipModel.body[0].lines.length > 0) {
                        var lines = tooltipModel.body[0].lines[0].split(',');
                        if (lines[0]) {
                            var html = '<div class="mb-1"><i class="fas fa-crown nps-fa fa-lg"></i> <strong>'+lines[0]+'</strong></div>';
                                html += '<div><i class="fas fa-crown average-fa fa-lg"></i> <strong>'+lines[1]+'</strong></div>';
                            $tooltip.html(html);
                            $tooltip.css('top', (position.top + window.pageYOffset + tooltipModel.caretY) - 60 + 'px');
                        } else {
                            $tooltip.html('アンケート回答数が少ないため結果がありません。');
                            $tooltip.css('top', (position.top + window.pageYOffset + tooltipModel.caretY) - 30 + 'px');
                        }
                        $tooltip.css('opacity', '1');
                        $tooltip.css('position', 'absolute');
                        $tooltip.css('left', (position.left + window.pageXOffset + tooltipModel.caretX) - 60 + 'px');
                        $tooltip.css('fontFamily', '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"');
                        $tooltip.css('fontSize', '11px');
                        $tooltip.css('fontStyle', tooltipModel._bodyFontStyle);
                        $tooltip.css('padding', '8px 8px');
                        $tooltip.css('pointerEvents', 'none');
                        $tooltip.show();
                    } else {
                        $tooltip.hide();
                    }
                }
            },
            responsive: true
        }
    };
    var chart = new Chart(ctx, chart_option);
    MC.chart[$canvas.attr('id')] = chart;
};

MC.view_pie_chart = function($canvas, data, show_label, bg_colors) {
    if (typeof MC.chart[$canvas.attr('id')] != 'undefined') {
        MC.chart[$canvas.attr('id')].destroy();
    }
    //console.log(data);
    var labels = Object.keys(data);
    //console.log(labels);
    var values = [];
    var bgColors = [];
    if (bg_colors) {
        $.each(bg_colors, function(i, bg_color) {
            if (bg_color) {
                bgColors[i] = bg_color;
            }
        });
    } else {
        bgColors = MC.default_colors.selections;
    }
    var $ul = $canvas.next('ul');
    $ul.empty();
    $.each(labels, function(i, l) {
        values.push(data[l]);
        var $badge = $('<span class="badge"></span>').text(l).css('backgroundColor', bgColors[i%10]);
        $ul.append($('<li class="list-inline-item"></li>').append($badge));
    });
    var ctx = $canvas[0].getContext('2d');
    //console.log(values);
    var chart_option = {
        type: 'pie',
        data: {
            datasets: [{
                data: values,
                backgroundColor: bgColors
            }]
        },
        options: {
            responsive: true,
            legend: {display: show_label}
        }
    };
    chart_option.data.labels = labels;
    var chart = new Chart(ctx, chart_option);
    MC.chart[$canvas.attr('id')] = chart;
};

/*
MC.view_nps_chart = function($canvas, data) {
    //console.log($canvas);
    //console.log(data);
    var ctx = $canvas[0].getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['推奨者('+data[0]+')', '中立者('+data[1]+')', '批判者('+data[2]+')'],
            datasets: [{
                data: data,
                backgroundColor: [
                    //'rgba(0, 137, 132, 0.2)',
                    //'rgba(255, 206, 86, 0.3)',
                    //'rgba(226, 61, 61, 0.3)'
                    'rgba(240, 98, 146, 1)',
                    'rgba(92, 107, 192, 1)',
                    'rgba(177, 186, 206, 1)',
                ]
            }]
        },
        options: {
            responsive: true,
            tooltips: {
                //enabled: false
                callbacks: {
                    label: function(tooltipItem, data) {
                        return data.datasets[0].data[tooltipItem.index];
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        }
    });
};
*/

MC.view_line_chart = function($canvas, data, min) {
    if (typeof MC.chart[$canvas.attr('id')] != 'undefined') {
        MC.chart[$canvas.attr('id')].destroy();
    }
    //console.log(data);
    var labels = Object.keys(data);
    var values = [];
    var bdrColors = [];
    var count = labels.length;
    $.each(labels, function(i, l) {
        values.push(data[l]);
    });
    var ctx = $canvas[0].getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    //'rgba(0, 137, 132, .2)'
                    //'rgba(0, 0, 0, 0)'
                    'rgba(0, 0, 0, 0)'
                ],
                borderColor: [
                    'rgba(240, 117, 29, 1.0)'
                ],
                borderWidth: 2,
                lineTension: 0
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: { min: min}
                }]
            },
            legend: {display: false},
            responsive: true
        }
    });
    MC.chart[$canvas.attr('id')] = chart;
};

MC.view_bar_chart = function($canvas, data) {
    if (typeof MC.chart[$canvas.attr('id')] != 'undefined') {
        MC.chart[$canvas.attr('id')].destroy();
    }
    var labels = Object.keys(data);
    var values = [];
    var step = 1;
    var bgColors = [];
    var bdrColors = [];
    var count = labels.length;
    var i=1;
    $.each(labels, function(i, l) {
        values.push(data[l]);
        // var rank = l / count;
        // if (rank < 0.3) {
        //     bgColors.push('rgba(255, 99, 132, 0.1)');
        //     bdrColors.push('rgba(255,99,132,1)');
        // } else if (rank > 0.8) {
        //     bgColors.push('rgba(153, 102, 255, 0.6)');
        //     bdrColors.push('rgba(153, 102, 255, 1)');
        // } else {
        //     bgColors.push('rgba(255, 206, 86, 0.3)');
        //     bdrColors.push('rgba(255, 206, 86, 1)');
        // }
        if (i=1) {
            bgColors.push('rgba(153, 102, 255, 0.6)');
            bdrColors.push('rgba(153, 102, 255, 1)');
        }
        else {
            bgColors.push('rgba(255, 206, 86, 0.3)');
            bdrColors.push('rgba(255, 206, 86, 1)');
        }
        i++;
        if (data[l] > 100) {
            step = 10;
        } else if (data[l] > 30) {
            step = 5;
        }
    });
    //console.log(values);
    var ctx = $canvas[0].getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: null,
                data: values,
                // backgroundColor: bgColors,
                // borderColor: bdrColors,
                
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                
                borderWidth: 1
            }]
        },
        options: {
            legend: {display: false},
            tooltips: {enabled: false},
            scales: {
                yAxes: [{
                    ticks: {
                        stepSize: step,
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    MC.chart[$canvas.attr('id')] = chart;
};

MC.get_replies = function(url) {
    //console.log(url);
    $('#report-history-pagination').empty();
    $('#replies').empty();
    MC.ajax_get(
        url,
        function(response) {
            $.each(response.data.replies, function(i, reply) {
                //console.log(reply.id);
                var $tr = $('#replies-dummy > tr').clone(true);
                $tr.data('reply-id', reply.id);
                $tr.data('reply-datetime', moment(reply.created_at).format('YYYY/M/D HH:mm'));
                $tr.find('.reply-id').text(reply.id);
                $tr.find('.reply-date').text(moment(reply.created_at).format('YYYY/M/D'));
                $tr.find('.reply-time').text(moment(reply.created_at).format('HH:mm'));
                $('#replies').append($tr);
            });
            if (response.pagination) {
                MC.render_pagination(response.pagination, $('#report-history-pagination'), function($a) {
                    MC.get_replies($a.attr('href'));
                });
            }
        },
        function(response) {
        },
        function() {
        }
    );
};

MC.get_answer = function(reply_id) {
    $('#reply-answer').empty();
    var url = '/ajax/answer/get_answer/'+reply_id+'/';
    MC.ajax_get(
        url,
        function(response) {
            var answer = response.data.answer;
            $.each(answer, function(i, ans) {
                //console.log(ans);
                var $card = $('#level-answer-dummy > .card').clone(true);
                $card.children('.card-header').text(ans.question);
                if (ans.type == 'level') {
                    var $level = $card.find('.level');
                    var min = parseInt(ans.min_level, 10);
                    var max = parseInt(ans.max_level, 10);
                    for (i=min; i<=max; i++) {
                        var badge_class = 'badge-light';
                        var list_class = '';
                        if (i == parseInt(ans.level, 10)) {
                            badge_class = 'badge-hairlogy';
                            list_class = 'h4';
                        }
                        $level.append($('<li></li>', {
                            addClass: 'list-inline-item px-1 '+list_class
                        }).html($('<span></span>', {
                            addClass: 'badge '+badge_class,
                            html: i
                        })));
                    }
                    if (ans.sub_answer) {
                        $card.find('.sub_answer').val(ans.sub_answer).show();
                    }
                } else if (ans.type == 'select_one') {
                    var $select_one = $card.find('.select_one');
                    //var selections = JSON.parse(ans.selections);
                    var selections = ans.selections;
                    $.each(selections, function(i, choice) {
                        var badge_class = 'badge-light';
                        var list_class = '';
                        if (choice.selection == ans.answer) {
                            badge_class = 'badge-hairlogy';
                            list_class = 'h4';
                        }
                        $select_one.append($('<li></li>', {
                            addClass: 'list-inline-item px-1 '+list_class
                        }).html($('<span></span>', {
                            addClass: 'badge '+badge_class,
                            html: choice.selection
                        })));
                    });
                    if (ans.sub_answer) {
                        $card.find('.sub_answer').val(ans.sub_answer).show();
                    }
                }
                //console.log($card);
                $('#reply-answer').append($card);
            });
        },
        function(response) {
        },
        function() {
        }
    );
};

$(function() {

    MC.stylist_id = $('#selected-stylist').data('stylist-id') || '';
    $('#report-nps-tab').data('stylist-id', MC.stylist_id);
    $('#report-graph-tab').data('stylist-id', MC.stylist_id);
    $('#report-history-tab').data('stylist-id', MC.stylist_id);
    // $('#comprehensive').data('stylist-id', MC.stylist_id);
    // $('#individual').data('stylist-id', MC.stylist_id);
    // $('#receipt').data('stylist-id', MC.stylist_id);

    if (MC.stylist_id == MC.login.stylist_id) {
        $('.stylist-name').text('あなた');
    } else {
        var stylist_name = $('#selected-stylist').text();
        $('.stylist-name').text(stylist_name+'さん');
    }
    var questionnaire_id = $('#select-term').data('questionnaire-id');
    //console.log(questionnaire_id);

    $('#report-history-tab .clickable').click(function(e) {
        e.preventDefault();
        var reply_id = $(this).parents('td').parents('tr').data('reply-id');
        var answer_datetime = $(this).parents('td').parents('tr').data('reply-datetime');
        MC.get_answer(reply_id);
        $('#reply-modal .answer-no').text(reply_id);
        $('#reply-modal .reply-datetime').text(answer_datetime);
        $('#reply-modal').modal('show');
    });

    $('#nps-select-stylist > a').click(function(e) {
        e.preventDefault();
        MC.stylist_id = $(this).data('stylist-id');
        $('#report-nps-tab').data('stylist-id', MC.stylist_id);
        $('#nps-selected-stylist').text($(this).text());
        MC.get_nps_transition(questionnaire_id);
        MC.get_nps_levels(questionnaire_id);
    });

    $('#select-stylist > a').click(function(e) {
        e.preventDefault();
        MC.stylist_id = $(this).data('stylist-id');
        $('#report-graph-tab').data('stylist-id', MC.stylist_id);
        var stylist_name = $(this).text();
        $('#selected-stylist').text(stylist_name);
        var term = $('#selected-term').data('term');
        MC.get_count_answers(questionnaire_id, term);
        MC.get_average_levels(questionnaire_id, term);
        $('.alert > .stylist-name').text(stylist_name+'さん');
    });

    $('#select-term > a').click(function(e) {
        e.preventDefault();
        $('#selected-term').text($(this).text());
        var term = $(this).data('term');
        $('#selected-term').data('term', term);
        MC.get_count_answers(questionnaire_id, term);
        MC.get_average_levels(questionnaire_id, term);
        MC.get_sub_question_answers(questionnaire_id, term);
    });

    $('#history-select-stylist > a').click(function(e) {
        e.preventDefault();
        MC.stylist_id = $(this).data('stylist-id');
        $('#report-history-tab').data('stylist-id', MC.stylist_id);
        $(this).data('stylist-id', MC.stylist_id);
        $('#history-selected-stylist').text($(this).text());
        MC.get_replies('/ajax/reply/get_replies/'+MC.stylist_id+'/0/');
    });
    /*
    $('#select-nps-term').change(function() {
        MC.get_nps_transition(questionnaire_id);
    });
    */

    MC.get_nps_transition(questionnaire_id);
    MC.get_replies('/ajax/reply/get_replies/'+MC.login.stylist_id+'/0/');
    // MC.get_count_answers(questionnaire_id, '30days');
    // MC.get_average_levels(questionnaire_id, '30days');
    // MC.get_nps_levels(questionnaire_id);
    // MC.get_sub_question_answers(questionnaire_id, '30days');
    MC.get_count_answers(questionnaire_id, 'one_year');
    MC.get_average_levels(questionnaire_id, 'one_year');
    MC.get_nps_levels(questionnaire_id);
    MC.get_sub_question_answers(questionnaire_id, 'one_year');

    $('#report-tab a[data-toggle=tab]').on('shown.bs.tab', function(e) {
        var stylist_id = $(this).data('stylist-id');
        if (stylist_id != MC.stylist_id) {
            var $tab = $($(this).attr('href'));
            $tab.find('.select-stylist a[data-stylist-id='+MC.stylist_id+']').trigger('click');
        }
    });

    $('.collapse').on('show.bs.collapse', function (e) {
        var id = $(this).attr('id');
        var $btn = $('.collapse-btn[data-target=\\#'+id+']');
        $btn.addClass('open');
    });
    $('.collapse').on('hide.bs.collapse', function (e) {
        var id = $(this).attr('id');
        var $btn = $('.collapse-btn[data-target=\\#'+id+']');
        $btn.removeClass('open');
    });

    $('#report-tab a[data-toggle=tab]').on('click', function(e) {
        MC.last_tab = '#'+$(this).attr('id');
        var tab = MC.last_tab;
        window.history.pushState(tab, '');
    });

    window.onpopstate = function (e){
        // 復元できないので無視
        if( ! e.state) {
            $('#report-nps-tab').tab('show');
        }
        var tab = e.state;
        $(tab).tab('show');
    };

});

