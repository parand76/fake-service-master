<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Error Page">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="/GlobalStaticContent/HttpErrors/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/GlobalStaticContent/HttpErrors/css/main-style.css" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Inconsolata&amp;v1">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Candal&amp;v1">

    <!--[if lte IE 8]>
    <script src="/GlobalStaticContent/HttpErrors/js/html5.js"></script>
    <![endif]-->
</head>

<body>
    <div id="container">
        <div id="stage" class="stage">
            <div id="clouds" class="stage"></div>
        </div>

        <div id="ticket">
            <section id="ticket_left">
                <p class="text1_a">Lost ın the clouds</p>
                <p class="text2_a">Flıght not found</p>
                <p class="text3_a">Error 403</p>
                <p class="text4_a">Sorry!</p>
                <p class="text5_a">From</p>
                <p class="text6_a">Somewhere</p>
                <p class="text7_a">To</p>
                <p class="text8_a">Nowhere</p>
                <p class="text9_a">Seat</p>
                <p class="text10_a text10_a_404">403</p>
                <p class="text11_a">Get ready for boarding,please go to gate:</p>
                <nav class="text12_a">
                    <ul>
                        <li><a href="#" style="color:#5D5D5D">Home</a></li>
                    </ul>
                </nav>
            </section>

            <section id="ticket_right">
                <p class="text1_b">Boardıng Pass</p>
                <p class="text2_b">Lost ın the clouds</p>
                <p class="text3_b">From</p>
                <p class="text4_b">Somewhere</p>
                <p class="text5_b">To</p>
                <p class="text6_b">Nowhere</p>
                <p class="text7_b">Seat</p>
                <p class="text8_b">403</p>
                <p class="text9_b">1</p>
                <p class="text10_b">Error 403</p>
            </section>
        </div>
    </div>

    <script src="/GlobalStaticContent/HttpErrors/js/jquery-3.5.1.min.js" type="text/javascript"></script>
    <script src="/GlobalStaticContent/HttpErrors/js/jquery.spritely.js" type="text/javascript"></script>

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('#clouds').pan({
                    fps: 40,
                    speed: 0.7,
                    dir: 'right',
                    depth: 10
                });
            });

            //set home page url
            var homepage = GetHomePageUrl();
            $('.text12_a > ul > li > a').attr('href', homepage);
        })(jQuery);

        function GetHomePageUrl() {
            var path = location.pathname;
            var splittedPathArr = path.split('/');

            return (splittedPathArr.length > 0 && (location.hostname.indexOf(".amadeus.com") != -1 || location.hostname === 'localhost')) ? location.origin + '/' + splittedPathArr[1] : location.origin;
        }
    </script>
</body>

</html>