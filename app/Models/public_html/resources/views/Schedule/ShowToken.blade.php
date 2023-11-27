<!DOCTYPE html>
<html>

<head>
    @include('header')
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

        <div class="pagetitle">
            <div class="d-flex justify-content-between">
                <h1>Token Generation</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('schedulemanager.index') }}"> ScheduleTable </a>
                        </li>
                    </ol>

                </nav>
            </div>
        </div>
        <div class="container">
            <form class="Enquiry AddForm" id="enquiry_type" novalidate>
                {{ csrf_field() }}
                <div class="row">
                    <div class=" col-12">
                        <label class="mt-2 mb-1 inputlabel">Docter<span style="color:red; font-size:15px">
                                *</span></label><br>
                        <select class="form-select  inputfield" aria-label="Default select example name" id="docter_id"
                            name="docter_id" autofocus>
                            <option hidden class="inputlabel" value="">Choose Docter</option>
                            @foreach ($Docter as $key)
                                <option class="inputlabel" value="{{ $key->id }}">
                                    {{ $key->firstname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="mt-2 mb-1 inputlabel">Choose Hospital<span
                                style="color:red; font-size:15px">*</span></label><br>
                        <select class="form-select inputfield" aria-label="Default select example name" id="hospital_id"
                            name="HospitalID" autofocus>
                            <option hidden class="inputlabel" value="">Choose Hospital</option>

                        </select>
                    </div>

                    <div class=" col-12">
                        <label class="mt-2 mb-1 inputlabel">Seesion Title<span style="color:red; font-size:15px">
                                *</span></label><br>
                        <input type="text" class="form-control mt-1 inputfield" id="session_name" name="EnquiryName"
                            pattern="[^0-9]+" placeholder="Enter session Title" autofocus required>
                    </div>
                    <div class=" col-12">
                        <label class="mt-3 mb-1 inputlabel">Date</label><br>
                        <input type="date" value="<?php echo date('Y-m-d'); ?>" class="form-control mt-1 inputfield"
                            id="date" name="date" pattern="[^0-9]+" min="<?php echo date('Y-m-d'); ?>" autofocus>
                    </div>

                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="mt-3 mb-1 inputlabel">Starting Time</label><br>
                        <input type="time" class="form-control mt-1 inputfield" id="starttingtime"
                            name="starttingtime" pattern="[^0-9]+" autofocus>
                    </div>
                    <div class="col-6">
                        <label class="mt-3 mb-1 inputlabel">Ending Time</label><br>
                        <input type="time" class="form-control mt-1 inputfield" id="endingtime" name="endingtime"
                            pattern="[^0-9]+" autofocus>
                    </div>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <label class="mt-3 mb-1 inputlabel">Time Duration</label><br>
                                <input type="text" class="form-control mt-1 inputfield" id="timeduration"
                                    name="timeduration" pattern="[^0-9]+" autofocus>
                            </div>
                            <div class="col-6">
                                <label class="mt-3 mb-1 inputlabel">Format<span style="color:red; font-size:15px">
                                        *</span></label><br>
                                <select class="form-select  inputfield" aria-label="Default select example name"
                                    id="format" name="format" autofocus>
                                    <option hidden class="inputlabel" value="">Choose One</option>
                                    <option class="inputlabel" value="min">Min</option>
                                    <option class="inputlabel" value="hr">hour</option>

                                </select>
                            </div>
                            <div class="col-12">
                                <label class="mt-3 mb-1 inputlabel">Number Of Token</label><br>
                                <input type="text" class="form-control mt-1 inputfield" id="Token" name="token"
                                    pattern="[^0-9]+" autofocus>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <label class="mt-3 mb-1 inputlabel">Select Days</label><br>
                            <div class="col-2">
                                <input type="checkbox" value="sunday" id="beforeFoodMrng">Sunday

                            </div>
                            <div class="col-2">
                                <input type="checkbox" value="monday" id="AfterFoodMorning">monday

                            </div>
                            <div class="col-2">
                                <input type="checkbox" value="tuesday" id="Noon">Tuesday

                            </div>
                            <div class="col-2">
                                <input type="checkbox" value="wednesday" id="night">Wednesday
                            </div>
                            <div class="col-2">
                                <input type="checkbox" value="thursday" id="night">Thursday
                            </div>
                            <div class="col-1">
                                <input type="checkbox" value="friday" id="night">Friday
                            </div>
                            <div class="col-1">
                                <input type="checkbox" value="saturday" id="night">Saturday
                            </div>
                        </div>

                    </div>
                </div>

                <div class=" text-end mt-3">
                    <button type="button" id="generateToken" class="btn savebtn  px-5 ">Generate </button>
                </div>

                <hr>
                <div class="text-end"><input type="checkbox" id="checkAll"> Check All/Uncheck All</div>
                <div class="row" id="card-container">
                </div>
                <div class=" text-end mt-3">
                    <button type="submit" class="btn  savebtn px-5 ">Save </button>
                </div>
            </form>

        </div>
    </main>
    @include('footer')
    <script>

$('#docter_id').on('change', function() {
    var selectedDoctorId = $(this).val();
    console.log(selectedDoctorId);

    $.get('/api/get-hospital-name/' + selectedDoctorId, function(data) {
        console.log(data);

        if (data.hospital_details.length > 0) {
            $('#hospital_id').html('<option value="">Choose Hospital</option>'); // Clear previous options

            data.hospital_details.forEach(function(hospital) {
                $('#hospital_id').append('<option  value="' + hospital.id + '">' + hospital.hospital_Name + '</option>');
            });
        } else {
            alert('Hospital details not found for the selected doctor');
        }
    });
});



        $("#generateToken").click(function() {
            var StartTime = $('#starttingtime').val();
            var EndTime = $('#endingtime').val();
            var Timeduration = $('#timeduration').val();
            $.ajax({
                type: "POST",
                url: "/api/generate-cards",
                data: {
                    startingTime: StartTime,
                    endingTime: EndTime,
                    timeduration: Timeduration,
                },
                success: function(response) {
                    // Handle the response from the server
                    if (response.cards) {
                        updateCardContainer(response.cards);
                    }
                },
                error: function(error) {
                    console.log(error);
                },
            });
        });


        function updateCardContainer(cards) {
            $("#card-container").empty(); // Clear the container first

            // Loop through the cards and create card elements
            $.each(cards, function(index, card) {
                var cardHtml = `
                <div class="col-2">
                    <div class="card">
                        <div class="card-body">
                            <input type="checkbox" data-time="${card.Time}" data-number="${card.Number}">
                            ${card.Time} Token:${card.Number}
                        </div>
                    </div>
                </div>`;
                $("#card-container").append(cardHtml);
            });
        }

        // Check All / Uncheck All functionality
        $("#checkAll").change(function() {
            var isChecked = $(this).is(":checked");
            $("#card-container input[type='checkbox']").prop("checked", isChecked);

            // Clear the checkedData array when "Uncheck All" is clicked
            if (!isChecked) {
                checkedData = [];
            } else {
                // Add all data to the array when "Check All" is clicked
                checkedData = $("#card-container input[type='checkbox']").map(function() {
                    return {
                        Time: $(this).data("time"),
                        Number: $(this).data("number")
                    };
                }).get();
            }

            // You can now access the checkedData array to see which checkboxes are checked.
            console.log(checkedData);
        });

        // Array to store checked data
        var checkedData = [];


        // Listen for checkbox changes and update the array
        $("#card-container").on("change", "input[type='checkbox']", function() {
            var $checkbox = $(this);
            var time = $checkbox.data("time");
            var number = $checkbox.data("number");

            if ($checkbox.is(":checked")) {
                // Add the checked data to the array
                checkedData.push({
                    Time: time,
                    Number: number
                });
            } else {
                // Remove the unchecked data from the array
                checkedData = checkedData.filter(function(item) {
                    return item.Time !== time || item.Number !== number;
                });
            }

            // You can now access the checkedData array to see which checkboxes are checked.
            console.log(checkedData);
        });




        $("#enquiry_type").validate({
            rules: {
                EnquiryName: {
                    required: true,
                    minlength: 2,
                    maxlength: 35,
                }
            },
            messages: {
                EnquiryName: {
                    required: "This field is required",
                    minlength: "At least 2 characters",
                    maxlength: "Maximum 35 characters",
                }
            },
            submitHandler: function(form) {
                var DocterName = $('#docter_id').val();
                var HospitalId = $('#hospital_id').val();
                var SessionTitile = $('#session_name').val();
                var Date = $('#date').val();
                var StartTime = $('#starttingtime').val();
                var EndTime = $('#endingtime').val();
                var TotalToken = $('#Token').val();
                var Timeduration = $('#timeduration').val();
                var Format = $('#format').val();

                let TokenGenerated = JSON.stringify(checkedData);
                let SelectedDays = JSON.stringify(SelectedDaysData);

                console.log(HospitalId);
                $.ajax({
                    url: "/api/schedules",
                    method: "POST",
                    timeout: 0,
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    data: {
                        docter_id: DocterName,
                        hospital_Id:HospitalId,
                        session_title: SessionTitile,
                        date: Date,
                        startingTime: StartTime,
                        endingTime: EndTime,
                        TokenCount: TotalToken,
                        timeduration: Timeduration,
                        format: Format,
                        tokens: TokenGenerated,
                        selecteddays:SelectedDays

                    },
                    beforeSend: function() {
                        $('.loader').show();
                        $('#EnquiryModal').modal('hide');
                        $('.mainContents').hide();
                        $('#ResponseImage').html("");
                        $('#ResponseText').text("");
                    },
                }).done(function(response) {
                    $('.mainContents').show();
                    $('.loader').hide();

                    console.log(response);
                    console.log(response.message);
                    var EnResult = JSON.stringify(response);
                    console.log(EnResult);
                    var EnResultObj = JSON.parse(EnResult);
                    if (EnResultObj.success == true) {
                        if (EnResultObj.code == "0") {

                            swal("Warning", response.message, "warning");
                        } else if (EnResultObj.code == "1") {

                            swal("Success", response.message, "success");
                        } else if (EnResultObj.code == "2") {

                            swal("Error", response.message, "error");
                        }
                    } else {
                        // Error icon
                        swal("Some Error Occurred!!!", "Please Try Again", "error");
                    }
                });
            }
        });




        $("#format").on("click", function() {
            var Timeduration = $('#timeduration').val();
            var StartTime = $('#starttingtime').val();
            var EndTime = $('#endingtime').val();
            if (Timeduration !== "") {
                $.ajax({
                    url: "/api/getTokenCount",
                    method: "POST",
                    data: {
                        timeduration: Timeduration,
                        startingTime: StartTime,
                        endingTime: EndTime,
                    },
                    success: function(data) {
                        console.log(data);
                        $("#Token").val(data
                            .max_token_count); // Update the "Number Of Token" field with the result
                    },
                    error: function() {

                    }
                });
            } else {
                // Clear the "Number Of Token" field if the time duration is empty
                $("#Token").val("");
            }
        });



        var SelectedDaysData = [];

// Add change event listener to all checkboxes
$('input[type="checkbox"]').on('change', function() {
    updateSelectedDaysData();
});

function updateSelectedDaysData() {
    SelectedDaysData = []; // Clear the array

    // Loop through all checkboxes and add checked values to the array
    $('input[type="checkbox"]:checked').each(function() {
        SelectedDaysData.push($(this).val());
    });

    // Log the selected days (you can remove this line in the actual implementation)
    console.log(SelectedDaysData);
}
    </script>


</body>

</html>
