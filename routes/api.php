<?php


//Widgets
Route::group([
                 "prefix"    => "widgets",
                 "as"        => "widgets.",
                 "namespace" => "Widgets",
             ],
    function () {

        //WidgetController.php
        Route::group([
                         "prefix" => "",
                         "as"     => "",
                     ],
            function () {

                //render
                Route::match(["get"], 'render/{public_id}',
                             [
                                 "as"   => "render",
                                 "uses" => "WidgetController@render",
                             ]
                );

            });

    });