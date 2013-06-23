



<?php

$wrap = new CS_REST_Lists($cm_list, $auth);

function get_actives($auth, $page_number)
{
    // last 365 days
    $date_from = date('Y-m-d', strtotime('-90 days'));

    // params: start date, page number, page size, order by, order direction
    $result = $auth->get_active_subscribers($date_from, $page_number, 1000, 'date', 'asc');

    if($result->was_successful()) {
        echo "<p>Got subscribers</p>";
        echo "<pre>";

        echo("ResultsOrderedBy ".$result->response->ResultsOrderedBy."\n");
        echo("OrderDirection ".$result->response->OrderDirection."\n");
        echo("PageNumber ".$result->response->PageNumber."\n");
        echo("PageSize ".$result->response->PageSize."\n");
        echo("RecordsOnThisPage ".$result->response->RecordsOnThisPage."\n");
        echo("TotalNumberOfRecords ".$result->response->TotalNumberOfRecords."\n");
        echo("NumberOfPages ".$result->response->NumberOfPages."\n");
        echo "</pre>";

    } else {
        echo "<pre>";
        echo '<p>Failed with code '.$result->http_status_code."</p>";
        var_dump($result->response);
        echo "</pre>";
    }

    return $result;
}

function add_results_to_stack($stack, $result)
{
    foreach($result->response->Results as $list) {
        $date = $list->Date;
        $d = substr($date, 0, 7);   // just get the month part of the $list->Date
        // could convert this to pretty text for month?
        array_push($stack, $d);
    }

    print_r("Size of stack: ".sizeof($stack));

    return $stack;
}

    // **-**

    // start of API calls
    //

    // get the first page of results
    $result = get_actives($wrap, 1);
    $stack = add_results_to_stack(array(), $result);

    // check if we need to get more pages
    // we've already got page 1; loop from 1 until < num_pages, and get additional pages if necessary
    $num_pages = $result->response->NumberOfPages;
    for($i = 1; $i < $num_pages; $i++) {
        $next_page = $i+1;
        $result = get_actives($wrap, $next_page);
        $stack = add_results_to_stack($stack, $result);
    }

    // $stack now contains the results for all _active_ subscribers
    // get data for graph
    $graph_data = array_count_values($stack);

    //
    // end of API calls

    // **-**


    // hard-coded test data
    // to use comment out from **-** to **-**
    // $graph_data = array("2012-06"=>67, "2012-07"=>185, "2012-08"=>663, "2012-09"=>112, "2012-10"=>160, "2012-11"=>234, "2012-12"=>1569, "2013-01"=>2059, "2013-02"=>824, "2013-03"=>559, "2013-04"=>526, "2013-05"=>1600, "2013-06"=>442);

    $php_keys = array_keys($graph_data);
     $js_keys = json_encode($php_keys);

    $php_vals = array_values($graph_data);
     $js_vals = json_encode($php_vals);


    // create data for the running total graph

    $graph_data_running = $graph_data;
    $len = sizeof($graph_data_running)-1;

    $keys = array_keys($graph_data);

    // set last element of running total as total_active_subscribers
    // then calculate previous months by working backwords
    $graph_data_running[$keys[$len]] = $total_active_subscribers;
    for ($i=$len-1; $i >= 0 ; $i--) {
        $graph_data_running[$keys[$i]] = $graph_data_running[$keys[$i+1]] - $graph_data[$keys[$i+1]];
    }

    $php_keys_running = array_keys($graph_data_running);
     $js_keys_running = json_encode($php_keys_running);

    $php_vals_running = array_values($graph_data_running);
     $js_vals_running = json_encode($php_vals_running);


    echo '<pre>';
        print_r($graph_data);
        print_r($graph_data_running);
    echo '</pre>';

?>





<script type="text/javascript">


jQuery(function () {
    var lineChartDataMonthly = {
            labels : <?php echo $js_keys; ?>,
            datasets : [
                {
                    fillColor : "rgba(151,187,205,0.5)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    data : <?php echo $js_vals; ?>
                }
            ]
        }

    var lineChartDataRunning = {
            labels : <?php echo $js_keys_running; ?>,
            datasets : [
                {
                    fillColor : "rgba(151,187,205,0.5)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    data : <?php echo $js_vals_running; ?>
                }
            ]
        }
    var optionsRunning = {bezierCurve : false};

    var myLine = new Chart(document.getElementById("canvas-graph-1").getContext("2d")).Line(lineChartDataMonthly);
    var myLine = new Chart(document.getElementById("canvas-graph-2").getContext("2d")).Line(lineChartDataRunning, optionsRunning);
});

</script>

<canvas id="canvas-graph-1" width="700" height="400"></canvas>
<canvas id="canvas-graph-2" width="700" height="400"></canvas>

