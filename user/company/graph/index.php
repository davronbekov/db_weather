<?php
/**
 * Created by Netco Telecom.
 * User: Otabek
 * Date: 02-May-20
 * Time: 6:44 PM
 */
?>


<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> DB Weather </title>
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="/db_weather/css/vendor.css">
    <link rel="stylesheet" href="/db_weather/css/app.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        canvas {
            opacity: 1;
            /*background-color: white;*/
        }

    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="app" id="app">

        <?php
            require '../pieces/header.php';
        ?>

        <?php
            require '../pieces/menu.php';
        ?>

        <div class="position-relative">
            <canvas height="256" width="256" id="relief-map" class="position-absolute"></canvas>
            <canvas height="256" width="256" id="point_canvas" class="position-absolute"></canvas>
            <canvas id="weather-map" width="256" height="256" class="position-absolute"></canvas>

        </div>



    </div>
</div>

<script src="/db_weather/js/vendor.js"></script>
<script src="/db_weather/js/app.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script>

    function $_GET(param) {
        let vars = {};
        window.location.href.replace(
            /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
            function (m, key, value) { // callback
                vars[key] = value !== undefined ? value : '';
            }
        );

        if (param) {
            return vars[param] ? vars[param] : null;
        }
        return vars;
    }

    let zoomLevel = Number($_GET("z") || 7);

    function findPixelByLatLng(lat_lng, zoom) {
        let [lat, lng] = lat_lng;
        let mapWidth = Math.pow(2, zoom) * 256;
        let x = (lng + 180) * (mapWidth / 360)
        let latRad = lat * Math.PI / 180;
        let mercN = Math.log(Math.tan((Math.PI / 4) + (latRad / 2)));
        let y = (mapWidth / 2) - (mapWidth * mercN / (2 * Math.PI));
        return [x, y]
    }

    let minLatLng = [45.833861, 55.710446]
    let maxLatLng = [36.849236, 73.398434]
    let minXY = findPixelByLatLng(minLatLng, zoomLevel).map(v => v.toFixed()/256)
    let maxXY = findPixelByLatLng(maxLatLng, zoomLevel).map(v => v.toFixed()/256 + 1)

    let [min_x, min_y, max_x, max_y,] = [...minXY, ...maxXY]

    function relief_tile(z, x, y) {
        return $('<img width="256" height="256">')
            .attr('crossOrigin', '')
            .attr('src', `https://cartodb-basemaps-b.global.ssl.fastly.net/light_all/${z.toFixed()}/${x.toFixed()}/${y.toFixed()}.png`)
    }

    function weather_tile(z, x, y) {
        return $('<img width="256" height="256">')
            .attr('crossOrigin', '')
            .attr('src', `https://a.sat.owm.io/vane/2.0/weather/TA2/${z.toFixed()}/${x.toFixed()}/${y.toFixed()}?appid=9de243494c0b295cca9337e1e96b00e2&fill_bound`);
        // .attr('src', `https://tile.openweathermap.org/map/temp_new/${z}/${x}/${y}.png?appid=01c2dab301eeeff7e02ebf1748f65faa`);

    }


    function rgbToHex(rgb) {
        let r = rgb[0], g = rgb[1], b = rgb[2];
        if (r > 255 || g > 255 || b > 255)
            throw "Invalid color component";
        return ((r << 16) | (g << 8) | b).toString(16);
    }


    function initSquareCanvas(canvas, size) {
        canvas.height = size;
        canvas.width = size;
    }


    function clip(n, minValue, maxValue) {
        return Math.min(Math.max(n, minValue), maxValue);
    }

    function clipByRange(n, range) {
        return n % range;
    }

    function pixelXYToLatLong(pixelX, pixelY, zoomLevel) {
        let lat, lng;
        let mapSize = Math.pow(2, zoomLevel) * 256;

        let n = Math.PI - ((2.0 * Math.PI * (clipByRange(pixelY, mapSize - 1) / 256)) / Math.pow(2.0, zoomLevel));

        lat = ((clipByRange(pixelX, mapSize - 1) / 256) / Math.pow(2.0, zoomLevel) * 360.0) - 180.0;
        lng = (180.0 / Math.PI * Math.atan(Math.sinh(n)));
        return [lat, lng]
    }


    function latLongToPixelXY(latitude, longitude, zoomLevel) {
        let minLatitude = -85.05112878;
        let maxLatitude = 85.05112878;
        let minLongitude = -180;
        let maxLongitude = 180;
        let mapSize = Math.pow(2, zoomLevel) * 256;

        latitude = clip(latitude, minLatitude, maxLatitude);
        longitude = clip(longitude, minLongitude, maxLongitude);

        let x = ((longitude + 180.0) / 360.0 * (1 << zoomLevel));
        let y = ((1.0 - Math.log(Math.tan(latitude * (Math.PI / 180.0)) + 1.0
            / Math.cos(latitude * (Math.PI / 180.0))) / Math.PI) / 2.0 * (1 << zoomLevel));

        let tilex = (Math.trunc(x));
        let tiley = (Math.trunc(y));
        let pixelX = clipByRange((tilex * 256) + ((x - tilex) * 256), mapSize - 1);
        let pixelY = clipByRange((tiley * 256) + ((y - tiley) * 256), mapSize - 1);
        return [tilex, tiley]
    }


    function findPixelByLatLng(lat_lng, zoom) {
        let [lat, lng] = lat_lng;
        let mapWidth = Math.pow(2, zoomLevel) * 256;
        let mapHeight = mapWidth;
        let x = (lng + 180) * (mapWidth / 360)
        let latRad = lat * Math.PI / 180;
        let mercN = Math.log(Math.tan((Math.PI / 4) + (latRad / 2)));
        let y = (mapWidth / 2) - (mapWidth * mercN / (2 * Math.PI));
        return [x, y]
    }

    $(async function () {
        const vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        const vh = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

        let relief_canvas = $('#relief-map')[0];
        let relief_ctx;
        if (relief_canvas) {
            relief_canvas.height = (max_y - min_y) * 256;
            relief_canvas.width = (max_x - min_x) * 256
            relief_ctx = relief_canvas.getContext('2d');
            relief_ctx.globalAlpha = 1;
        }

        let weather_canvas = $('#weather-map')[0];
        weather_canvas.height = (max_y - min_y) * 256;
        weather_canvas.width = (max_x - min_x) * 256
        let weather_ctx = weather_canvas.getContext('2d');
        let count = 0;

        // loadMapReqursively(0, 0, 0, 0, zoomLevel);


        function loadMap() {
            console.log(max_y - min_y)

            if (relief_ctx) {
                for (let y = 0; y < max_y - min_y; y++) {
                    for (let x = 0; x < max_x - min_x; x++) {
                        console.log(zoomLevel)
                        relief_tile(zoomLevel, min_x + x, min_y + y).on('load', function () {
                            relief_ctx.drawImage(this, x * 256, y * 256, 256, 256);
                            weather_tile(zoomLevel, min_x + x, min_y + y).on('load', function () {
                                weather_ctx.drawImage(this, x * 256, y * 256, 256, 256);
                            });
                        });
                    }
                }
            } else {
                for (let y = 0; y < Math.pow(2, zoomLevel); y++) {
                    for (let x = 0; x < Math.pow(2, zoomLevel); x++) {

                        weather_tile(zoomLevel, x, y).on('load', function () {
                            weather_ctx.drawImage(this, x * 256, y * 256, 256, 256);
                            // if (x === (Math.pow(2, zoomLevel) - 1) && y === (Math.pow(2, zoomLevel) - 1)) {
                            //   setTimeout(() => {
                            //     for (let y = 0; y < Math.pow(2, zoomLevel)*256; y++) {
                            //       for (let x = 0; x < Math.pow(2, zoomLevel)*256; x++) {
                            //         setTimeout(()=>show_color_value(x, y), 1000)
                            //       }
                            //     }
                            //     console.log("FINISH")
                            //   }, 1000)
                            // }

                        })
                    }
                }
            }
        }

        loadMap();


        let valuesGradient = [
            [-40, [130, 22, 146, 1]],
            [-30, [130, 87, 219, 1]],
            [-20, [32, 140, 236, 1]],
            [-10, [32, 196, 232, 1]],
            [0, [35, 221, 221, 1]],
            [10, [194, 255, 40, 1]],
            [20, [255, 240, 40, 1]],
            [25, [255, 194, 40, 1]],
            [30, [252, 128, 20, 1]],
        ].map(function ([_value, _color]) {
            return [_value, _color.slice(0, -1)];
        });


        function findValFromRGBColor(color, valuesGradient) {
            color = Array.from(color);
            for (let index = 0; index < valuesGradient.length; index++) {
                let [grad_value, grad_color] = valuesGradient[index];
                let next = valuesGradient[index + 1];
                if (next) { // is not last
                    let [next_value, next_color] = next;

                    let grad_diff_color = grad_color.map(function (v, i) {
                        return next_color[i] - v;
                    });

                    let diff_color = color.map(function (v, i) {
                        return v - grad_color[i];
                    });
                    let is_valid_range = grad_diff_color.map(function (v, i) {
                            if (v > 0 && diff_color[i] > 0) {
                                return v >= diff_color[i]
                            } else if (v < 0 && diff_color[i] < 0) {
                                return v <= diff_color[i];
                            } else {
                                let diff = Math.abs(v - diff_color[i])
                                return diff <= 3 || diff_color[i] === 0;
                            }
                        }
                    ).every(function (v) {
                        return v;
                    });


                    if (is_valid_range) {
                        let abs_grad_diff_color = grad_diff_color.map(Math.abs);
                        let max_index = abs_grad_diff_color.indexOf(Math.max(...abs_grad_diff_color));
                        if (grad_diff_color[max_index] === 0) {
                            return grad_value;
                        }
                        return grad_value + diff_color[max_index] / grad_diff_color[max_index] * (next_value - grad_value);
                    }
                } else {

                    console.error("FUCK", color)
                    return grad_value
                }
            }
        }


        function findPos(obj) {
            var curleft = 0, curtop = 0;
            if (obj.offsetParent) {
                do {
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                } while (obj = obj.offsetParent);
                return {x: curleft, y: curtop};
            }
            return undefined;
        }


        let p_can = $('#point_canvas')[0];
        p_can.width = weather_canvas.width;
        p_can.height = weather_canvas.height;
        $(weather_canvas).mousedown(function (e) {
            console.log("WTF")

            let pos = findPos(this);
            let x = e.pageX - pos.x;
            let y = e.pageY - pos.y;
            show_color_value(x, y);
        });

        function show_color_value(x, y) {
            let rbga_color = weather_ctx.getImageData(x, y, 1, 1).data;
            let p_ctx = p_can.getContext("2d");
            let value = findValFromRGBColor(rbga_color.slice(0, -1), valuesGradient);
            if (value === 30) {
                p_ctx.fillStyle = "red";
            } else {
                p_ctx.fillStyle = "black";
            }
            p_ctx.fillRect(x, y, 10, 10);
            console.warn("Clicked on : x=", x, "y=", y);
            // console.warn("Color at this point: ", rbga_color);
            console.warn("Temperature", value)
        }


        //
        // $(canvas).mousedown(function (e) {
        //   let pos = findPos(this);
        //   let x = e.pageX - pos.x;
        //   let y = e.pageY - pos.y;
        //   let [lat, lng] = pixelXYToLatLong(x, y, zoomLevel);
        //   console.log(lat, lng);
        //   console.log(x, y);
        //   console.log(latLongToPixelXY(lat, lng, zoomLevel));
        // });

        // show_color_value(...findPixelByLatLng([41.311081, 69.240562], zoomLevel))
    })
</script>
</body>
</html>
