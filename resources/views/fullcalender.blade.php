<!DOCTYPE html>
<html>

<head>
    @include('header')
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
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
<div class="container">


    <div id='calendar'></div>

</div>
</main>

@include('footer')
<script type="text/javascript">



$(document).ready(function () {



    /*------------------------------------------

    --------------------------------------------

    Get Site URL

    --------------------------------------------

    --------------------------------------------*/

    var SITEURL = "{{ url('/') }}";



    /*------------------------------------------

    --------------------------------------------

    CSRF Token Setup

    --------------------------------------------

    --------------------------------------------*/

    $.ajaxSetup({

        headers: {

        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });



    /*------------------------------------------

    --------------------------------------------

    FullCalender JS Code

    --------------------------------------------

    --------------------------------------------*/

    var calendar = $('#calendar').fullCalendar({

                    editable: true,

                    events: SITEURL + "/fullcalender",

                    displayEventTime: false,

                    editable: true,

                    eventRender: function (event, element, view) {

                        if (event.allDay === 'true') {

                                event.allDay = true;

                        } else {

                                event.allDay = false;

                        }

                    },

                    selectable: true,

                    selectHelper: true,

                    select: function (start, end, allDay) {

                        var title = prompt('Event Title:');

                        if (title) {

                            var start = $.fullCalendar.formatDate(start, "Y-MM-DD");

                            var end = $.fullCalendar.formatDate(end, "Y-MM-DD");

                            $.ajax({

                                url: SITEURL + "/fullcalenderAjax",

                                data: {

                                    title: title,

                                    start: start,

                                    end: end,

                                    type: 'add'

                                },

                                type: "POST",

                                success: function (data) {

                                    displayMessage("Event Created Successfully");



                                    calendar.fullCalendar('renderEvent',

                                        {

                                            id: data.id,

                                            title: title,

                                            start: start,

                                            end: end,

                                            allDay: allDay

                                        },true);



                                    calendar.fullCalendar('unselect');

                                }

                            });

                        }

                    },

                    eventDrop: function (event, delta) {

                        var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");

                        var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");



                        $.ajax({

                            url: SITEURL + '/fullcalenderAjax',

                            data: {

                                title: event.title,

                                start: start,

                                end: end,

                                id: event.id,

                                type: 'update'

                            },

                            type: "POST",

                            success: function (response) {

                                displayMessage("Event Updated Successfully");

                            }

                        });

                    },

                    eventClick: function (event) {

                        var deleteMsg = confirm("Do you really want to delete?");

                        if (deleteMsg) {

                            $.ajax({

                                type: "POST",

                                url: SITEURL + '/fullcalenderAjax',

                                data: {

                                        id: event.id,

                                        type: 'delete'

                                },

                                success: function (response) {

                                    calendar.fullCalendar('removeEvents', event.id);

                                    displayMessage("Event Deleted Successfully");

                                }

                            });

                        }

                    }



                });



    });





    function displayMessage(message) {

        toastr.success(message, 'Event');

    }



</script>



</body>

</html>
