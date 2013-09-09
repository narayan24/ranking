<?php
class Charts
{
  function chartData($data, $se_ident, $domain) {
    $avg = array();

    foreach($data[$domain] as $key => $dates) {
      $sum = 0;
      foreach($dates as $date => $se) {
        $sum+= $se[$se_ident];

        $se[$se_ident] = (int)str_replace('>', '', $se[$se_ident]);
        $avg[$date]['val']+= $se[$se_ident];
        $avg[$date]['cnt']++;
      }
    }

    foreach($avg as $date => $sum) {
      $return[$date. ' 4:00AM'] = $sum['val'] / $sum['cnt'];
    }

    return $return;
  }

  function chart($chart, $data, $domain)
  {
    $data1 = $this->chartData($data, 'g', $domain);
    $data2 = $this->chartData($data, 'b', $domain);

    $str1 = '';
    foreach($data1 as $key => $val) {
      $str1.= '[\'' . $key . '\', ' . $val . '], ';
    }
    $str1 = ereg_replace(', $', '', $str1);

    $str2 = '';
    foreach($data2 as $key => $val) {
      $str2.= '[\'' . $key . '\', ' . $val . '], ';
    }
    $str2 = ereg_replace(', $', '', $str2);
    ?>
    <script type="text/javascript">
    $(document).ready(function(){
      var line1=[<?=$str1?>];
      var line2=[<?=$str2?>];
      var plot1 = $.jqplot('<?=$chart?>', [line1, line2], {
          title:'Average Google and Bing position',
          series:[
            {label:'Google'},
            {label:'Bing'}
          ],
          legend: { show:true, placement: 'outsideGrid' },
          axes:{
            xaxis:{
              renderer:$.jqplot.DateAxisRenderer,
              tickOptions:{
                formatString:'%b&nbsp;%#d'
              }
            },
            yaxis:{
              tickOptions:{
                formatString:'%.2f'
                }
            }
          },
          highlighter: {
            show: true,
            sizeAdjust: 7.5
          },
          cursor: {
            show: false
          }
      });
    });
    </script>
    <div id="<?=$chart?>" style="height:300px; width:600px;"></div>
    <div class="code prettyprint">
    <pre class="code prettyprint brush: js"></pre>
    </div>
    <?php
  }
}
