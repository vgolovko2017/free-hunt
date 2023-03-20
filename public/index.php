<?php

require '../bootstrap.php';

use Src\Utils;

$filter_value = false;
$filter_values = [
    'All',
    'PHP',
    'Web programming',
    'Website development',
    'HTML and CSS'
];

$url_components = parse_url($_SERVER['REQUEST_URI']);
parse_str($url_components['query'], $params);
$fvalue = $params['fvalue'] ?? null;

if ($fvalue && in_array($fvalue, $filter_values)) {
    if ($fvalue != $filter_values[0]) {
        $filter_value = $fvalue;
    }
}

$chart_data = $params['chart_data'] ?? null;
if ($chart_data && $chart_data == 'get') {
    $arr = Utils::setInitialChartData();
    $projects = Utils::getProjects($dbConnection, $filter_value);
    
    foreach($projects as $project) {
        Utils::collectRangeChartDataByBudget($project['budget'], $arr);
    }

    die(json_encode(array_values($arr)));
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link href="css/custom.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"
            integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF"
            crossorigin="anonymous"></script>

    <title>FreelanceHunt get projects example</title>
</head>

<script type="text/javascript">
    $(document).ready(function() {
        let url_params = "?chart_data=get";
        
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const fvalue = urlParams.get('fvalue');

        if (fvalue) {
            url_params += "&fvalue=" + fvalue;
        }

        $.ajax({
            url: url_params,
            method: "GET",
            success: function(data) {
                data = jQuery.parseJSON(data);

                let name = [];
                let value = [];

                for (let i in data) {
                    name.push(data[i].name);
                    value.push(data[i].value);
                }

                let chartdata = {
                    labels: name,
                    datasets: [{
                        label: 'Pie chart',
                        backgroundColor : [
                            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'
                        ],
                        hoverBackgroundColor: 'rgba(230, 236, 235, 0.75)',
                        hoverBorderColor: 'rgba(230, 236, 235, 0.75)',
                        data: value
                    }]
                };
                
                let graphTarget = $("#graphCanvas");
                let barGraph = new Chart(graphTarget, {
                    type: 'pie',
                    data: chartdata,
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
</script>
<body>
<header>
    <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <strong>FreelanceHunt get projects example</strong>
            </a>
        </div>
    </div>
</header>

<div class="container">
    <div class="card-body">
        <p>Pie Chart</p>
        <div class="card" id="chart-container">
            <canvas id="graphCanvas" width="300" height="300"></canvas>
        </div>
    </div>
    <div class="dropdown" style="padding-top: 30px;">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Choose the filter
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <?php foreach ($filter_values as $fvalue) { ?>
                <a class="dropdown-item" href="?fvalue=<?php echo $fvalue; ?>"><?php echo $fvalue; ?></a>
            <?php } ?>
        </div>
    </div>

    <div class="text-center"><h2>List of freeHunt projects</h2></div>
    <div class="bd-e-row">
        <div class="bd-e">
		    <div class="container">
                <div class="row">
				    <div class="col textellipsis"><b>Projects name</b></div>
                    <div class="col textellipsis"><b>Skills</b></div>
                    <div class="col textellipsis"><b>Link</b></div>
                    <div class="col textellipsis"><b>Budget</b></div>
                    <div class="col textellipsis"><b>Employer Login</b></div>
                    <div class="col textellipsis"><b>Employer Name</b></div>
		        </div>	
                <?php $projects = Utils::getProjects($dbConnection, $filter_value); ?>
                <?php foreach($projects as $project) { ?>
			        <div class="row">
				        <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['pname']; ?>"><?php echo $project['pname']; ?></div>
    				    <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['sname']; ?>"><?php echo $project['sname']; ?></div>
	    			    <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['link']; ?>"><a href="<?php echo $project['link']; ?>" target="_blank">Link to project</a></div>
		    		    <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['budget'] ?? '-'; ?>"><?php echo $project['budget'] ?? '-'; ?></div>
			    	    <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['employer_login'] ?? '-'; ?>"><?php echo $project['employer_login'] ?? '-'; ?></div>
                        <div class="col textellipsis" data-toggle="tooltip" title="<?php echo $project['employer_name'] ?? '-'; ?>"><?php echo $project['employer_name'] ?? '-'; ?></div>
		            </div>	
                <?php } ?>
    	    </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container text-center pt-4">
        <span>Copyright © 2023 Free Slava Holovko Nikolaev Software Foundation. All Rights Reserved.</span>
    </div>
</footer>

</body>
</html>
