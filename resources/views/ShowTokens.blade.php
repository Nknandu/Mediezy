<!DOCTYPE html>
<html>

<head>
    @include('header')

    <style>

    </style>
</head>


<body>

    <!-- ======= Header ======= -->

    <header id="header" class="header fixed-top">
        @include('navbar')
    </header>

    <!-- End Header -->
    <!-- ======= Sidebar ======= -->

    <aside id="sidebar" class="sidebar ps-0">
        @include('sidebar')
    </aside>



    <main id="main">



        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                    type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Today</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                    type="button" role="tab" aria-controls="profile-tab-pane"
                    aria-selected="false">Profile</button>
            </li>


        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                tabindex="0">
                <div class="card">
                    <div class="carditem m-2">
                        <h3 class="m-0"><span>Today,{{ now()->format('j F Y') }}</span>
                        </h3>
                        <div class="calendar">
                            @if (in_array(date('N'), [6, 7]))
                                Holiday
                            @else
                                Working Day
                            @endif
                            <img src="{{ url('assets/images/calendarimg.png') }}" alt="">
                        </div>
                    </div>
                </div>

                <div class="row" id="Token-container">


                </div>
            </div>
            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                tabindex="0">...</div>

        </div>
    </main>
    @include('footer')

    <script>
        var settings = {
            "url": "http://127.0.0.1:8000/api/schedule/2023-11-19",
            "method": "GET",
            "timeout": 0,
            "headers": {
                "Authorization": "Bearer 90dSbr5QXqc4S19m0r3xUOZRDkIQHcOOozudkz1Ka8ea5881"
            },
        };

        $.ajax(settings).done(function(response) {
            console.log(response);

            // Assuming response.tokens is a JSON string
            var tokens = JSON.parse(response.schedule.tokens);

            // Clear existing content in Token-container
            $('#Token-container').empty();

            // Loop through the tokens and create cards dynamically
            tokens.forEach(function(token) {
                var cardHtml = '<div class="col-md-4 mb-3">' +
                    '<div class="card">' +
                    '<div class="card-body">' +
                    '<h5 class="card-title">Token Number: ' + token.Number + '</h5>' +
                    '<p class="card-text">Time: ' + token.Time + '</p>' +
                    '<p class="card-text">Tokens: ' + token.Tokens + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                // Append the card HTML to Token-container
                $('#Token-container').append(cardHtml);
            });
        });
    </script>


</body>

</html>
