<?php
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery UI Autocomplete - Remote datasource</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <style>
        .ui-autocomplete-loading {
            background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
        }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            function log( message ) {
                $( "<div>" ).text( message ).prependTo( "#log" );
                $( "#log" ).scrollTop( 0 );
            }

            $( "#birds" ).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "api/v1/search/motorcycle",
                        dataType: "json",
                        data: {
                            full_search: request.term
                        },
                        success: function(data) {
                            var ret = [];
                            data.data.forEach(function(entry) {
                                ret.push({
                                    label: entry.name,
                                    value: entry.id
                                });
                            });
                            response(ret);
                        }
                    });
                },
                minLength: 2,
                select: function( event, ui ) {

                    log( ui );
                }
            });


            $( "#parts" ).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "api/v1/search/part",
                        dataType: "json",
                        data: {
                            full_search: request.term
                        },
                        success: function(data) {
                            var ret = [];
                            data.data.forEach(function(entry) {
                                ret.push({
                                    label: entry.name,
                                    value: entry.id
                                });
                            });
                            response(ret);
                        }
                    });
                },
                minLength: 2,
                select: function( event, ui ) {

                    log( ui );
                }
            });
        } );
    </script>
</head>
<body>

<div class="ui-widget">
    <label for="birds">Bike: </label>
    <input id="birds">
</div>


<div class="ui-widget">
    <label for="birds">Bike Parts: </label>
    <input id="parts">
</div>

<div class="ui-widget" style="margin-top:2em; font-family:Arial">
    Result:
    <div id="log" style="height: 200px; width: 300px; overflow: auto;" class="ui-widget-content"></div>
</div>


</body>
</html>
