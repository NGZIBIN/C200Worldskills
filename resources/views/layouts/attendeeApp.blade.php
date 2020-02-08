<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('attendeeApp.name', 'Event Booking Platform') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/attendee.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navigation.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


</head>
<script>
    $(document).ready(function() {
        var ticket = 0;
        var total = 0;
        var ticket_name = "";
        $("#btnSubmit").prop('disabled', true);
        $("#btnConfirm").prop('disabled', true);
        // if ($ticket=>ticket_left <= 0){
        //
        // }
        $("h6 [type=checkbox]").change(function () {
            ticket = 0;
            ticket_name = "";
            $("h6 [type=checkbox]:checked").each(function() {
                var int = $(this).val();
                var hiddenValue = parseInt($("#"+int).val());

                $("#btnSubmit").prop('disabled', false);
                ticket += hiddenValue;
                ticket_name += $("#"+int).attr("name")+" ";
            });

            $("#ticketName").html(ticket);
            $("#totalCost").html(ticket);
        });
        $("[name=session]").change(function () {
            var sessionTicket = 0;
            $("[name=session]:checked").each(function () {
                var int = parseInt($(this).val());
                sessionTicket += int;
            });
            $("#sessionCost").html(sessionTicket);
            total = sessionTicket + ticket;
            $("#totalCost").html(sessionTicket + ticket);

        });
        $("#btnSubmit").click(function () {
            $("[name=itemname]").val(ticket_name);
            $("[name=itemprice]").val(ticket);
            $("#item_modal").modal('show');


        });
        paypal.Button.render({
            env: 'sandbox',
            client: {
                sandbox: 'AbObdMqd0OOghYBQcgKpWa_9c_q1TeRyWHjRjj758C5xJFNgQvkvOZWd-5I4SXmfGJP4l5WezwWUUNHK'
            },
            commit: true, // Show paypal button

            payment: function (data, actions) {
                var payment = setupPayment();
                return actions.payment.create(payment);

            },
            onApprove: function(data, actions) {
                    return actions.payment.execute().then(function (response) {
                        $("#btnConfirm").prop('disabled', false);
                        var message = "";
                        message += "Purchase Success!<br/>";
                        message += "Click the confirm button to continue";
                        // message += "<a href='http://127.0.0.1:8000/attendee/home/worldskills-conference-2019'>Confirm!</a>"
                        $("#msg").html(message);
                        $("#item_modal").modal("hide");
                    });
                }

        }, '#paypal-button');
    });
    function setupPayment() {
        ticket = 0;
        ticket_name = "";
        $("h6 [type=checkbox]:checked").each(function() {
            var int = $(this).val();
            var hiddenValue = parseInt($("#"+int).val());
            $("#btnSubmit").prop('disabled', false);
            ticket += hiddenValue;
            ticket_name += $("#"+int).attr("name")+" ";
        });
        var currency = "SGD";
        var total = 0;
        var itemList = [];
        var itemName = ticket_name;
        var itemPrice = ticket;


        var item = {name: itemName, description: "", quantity: 1, price: itemPrice, currency: currency};
        itemList[itemList.length] = item;
        total = parseFloat(ticket);
        var payment = {
            payment: {
                transactions: [
                    {
                        amount: {total: total, currency: currency},
                        item_list: {items: itemList}
                    }
                ]
            }
        }
        return payment;
    }

</script>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('attendeeApp.name', 'Event Booking Platform') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>

<!-- Scripts -->

</html>
