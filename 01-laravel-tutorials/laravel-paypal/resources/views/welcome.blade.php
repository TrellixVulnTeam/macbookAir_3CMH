<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    </head>
    <body>
        <form action="" id="form">@csrf</form>
        
        <div id="paypal-button-container"></div>

        {{-- jquery --}}
        <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous">
        </script>

        {{-- paypal --}}
        <script src="https://www.paypal.com/sdk/js?client-id=sb&vault=true&currency=BRL"></script>
        
        
        <script>
            $(function() {
                // getToken();
            });
    
            // criar e buscar planos
            function getToken() {
                $.ajax({
                    url: "http://localhost/api/paypal-get-token",
                    method: "GET"

                }).fail(function() {
                    alert("Fail request load...");

                }).done(function(response) {
                    console.log(response);
                    console.log('=====================');
                    // createProduct(response.token);
                    createPlan(response.token);

                });
            }

            function createProduct(token) {
                var data = $('#form').serialize() + '&token=' + token;
                $.ajax({
                    url: "http://localhost/api/paypal-create-product",
                    method: "POST",
                    data: data

                }).fail(function() {
                    alert("Fail request load...");

                }).done(function(response) {
                    console.log(response);

                });
            }

            function createPlan(token) {
                var data = $('#form').serialize() + '&token=' + token;
                $.ajax({
                    url: "http://localhost/api/paypal-create-plan",
                    method: "POST",
                    data

                }).fail(function() {
                    alert("Fail request load...");

                }).done(function(response) {
                    console.log(response);

                });
            }
            
            // paypal buttons
            paypal.Buttons({

                createSubscription: function(data, actions) {
                    return actions.subscription.create({
                        'plan_id': 'P-37D465776C346112CLUXT44A'
                    });
                },
                // I-P324M3J8P8S7
                onApprove: function(data, actions) {
                    alert('You have successfully created subscription ' + data.subscriptionID);
                    return fetch('http://localhost/api/paypal-transaction-complete', {
                        method: 'post',
                        headers: {
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            data: data
                        })
                    });
                },

                onError: function (err) {
                    console.log(err)
                }

            }).render('#paypal-button-container');

        </script>
    </body>
</html>
