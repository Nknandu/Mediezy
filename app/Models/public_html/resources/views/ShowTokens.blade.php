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

                    {{-- <--------get the tokens--------> --}}
                </div>
            </div>
            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                tabindex="0">...</div>

        </div>
    </main>
    @include('footer')


    <script>
      var settings = {
  "url": "/api/today-schedule",
  "method": "GET",
  "timeout": 0,
  "headers": {
    "Accept": "application/json",
    "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiNTI2MTdiODU3OTNhYWJhOWI2N2ExOTM5ZWZhNzM2ODM0YTM5YjNjOGYwYjQzZjI4ZjM3YWQwMDc5ZDY3NmZmNDA2M2QwMGFhYTA5OTAwNGUiLCJpYXQiOjE2OTkzNTI4NzkuMDM2OTAyLCJuYmYiOjE2OTkzNTI4NzkuMDM2OTA3LCJleHAiOjE3MzA5NzUyNzguNjQ5NDAyLCJzdWIiOiIyMiIsInNjb3BlcyI6W119.jzVFenM6-rMwupu6GPxmumNgBLbzrJzaATaG5v5ooCflb-K63lqv7kgfk5UwYhMJTAVdLuojh8slu9-lexDNQXKqF_KXgorHbZ7P_ca9ch-QUkn3BqLxriUAv_BiNeVId605kPVATirOl8Lc07Pi6Z23c7PcyTePTFbV2_x6DrS75qQOpqvXUcx7Yw8MMDBy_l1X3H2nq0gvp69kbCz2rdq7R8BHnHW1Ik2kmYAwfVYTxGQ3_Ab8D8GaylrUQk6AKgrzg589jXQNaX_p3je3scO8jLMT91aQBEOoAM-il_-DNkmeXZUA24riew-OT5COJaJR8imSMxMS1L4j6MyhTOI0puy2qA-RK9mkfN_lmB3MU60DAGrkBKehsMHyZLxk9u1BqECt14V5Y16TpUBo3DHB2geuoihQoJt0Z3Dasu-QONiZZL8FZ_19eglbf-PejC-1HVQtr_VfEGOVs9xoh7v3eTJQqWmY0UIpDTzNcH5SsrqImEJZo0vgJxRWflnevQVWhjYVoVLfjeefGM2bfJDuq9Ocfy3JoRig4rzpUpN2tbRAd7NKufezuXE7u46xUjJSJoKp4rXNElPLztSmDt0Au8ek8A18YoF0h_EljQwdzEkQEtgXw1JkuuPM9cZYTMpKtP96YYmJ-dcw52bDGmS7Wzm1Tqmy1Cqv7uuzBZg"
  },
};

// Perform the AJAX request to get today's schedule
$.ajax(settings).done(function(response) {
  console.log(response);

  // Parse the todaytokens JSON string into a JavaScript array
  var todayTokens = JSON.parse(response.todaytokens);

  console.log(todayTokens);
  todayTokens.forEach(function(token) {
  var cardHtml = `
    <div class="col-2">
      <div class="card">
        <div class="card-body">
          ${token.Time} <br>
          Token: ${token.Number}
        </div>
      </div>
    </div>`;
  $("#Token-container").append(cardHtml); // Use .Tokencontainer for the class selector
});
});

    </script>
</body>

</html>
