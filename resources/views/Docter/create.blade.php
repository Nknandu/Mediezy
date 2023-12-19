<!DOCTYPE html>
<html>

<head>
    @include('header')
</head>
<style>
    .password-input {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }

    .image-preview {
        width: 100px;
        height: 100px;
        background-color: #f0f0f0;
        background-image: url('');
        /* Set initial image here */
        background-size: cover;
        background-position: center;
        border: 2px solid #ccc;
        border-radius: 50%;
    }
</style>

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



    <main id="main" class="main">





        <div class="wrapper">
            <!--CONTENTS-->
            <div class="container-fluid mainContents">
                <div class="card card-body main_card mt-2 shadow-lg p-3 mb-2">

                    <div class="text-end">


                    </div>


                    <form class="EnquiryAdd AddFormEnquiry" id="Enquiry_form" novalidate enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-6">
                                <label class="mt-2 mb-1 inputlabel">Profile Picture</label><br>
                                <input type="file" class="form-control mt-1" id="profile_picture" name="staff_image"
                                    accept="image/*">
                            </div>
                            <div class="col-xl-6 col-lg-6 col-6">
                                <div class="image-preview rounded-circle ml-3" id="imagePreview"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel formlabel" for="first_name">First Name<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <input type="text" class="form-control mt-1 inputfield" id="first_name"
                                    name="FirstName" placeholder="" autofocus required>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel formlabel" for="second_name">Last Name<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <input type="text" class="form-control mt-1 inputfield" id="second_name"
                                    name="SecondName" placeholder="" autofocus required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel formlabel" for="specialization">Specialization<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <select class="form-select  inputfield" aria-label="Default select example name"
                                    id="specialization" name="Specialization" autofocus>
                                    <option hidden class="inputlabel" value="">Choose specialization</option>
                                    @foreach ($specialization as $key)
                                        <option class="inputlabel" value="{{ $key->id }}">
                                            {{ $key->specialization }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="gender">Gender</label>
                                <select class="form-select inputfield" aria-label="Default select example name"
                                    id="gender" name="Gender" autofocus>
                                    <option hidden class="inputlabel" value="0"> Choose Gender</option>
                                    <option class="inputlabel" value="1"> Male</option>
                                    <option class="inputlabel" value="2"> Female</option>
                                    <option class="inputlabel" value="3"> Others</option>
                                </select>
                            </div>
                        </div>

                        <div class="hospital-container">
                            <div class="col-xl-12 col-lg-12 col-12 mb-3 hospital-row">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-6 col-12 mb-3">
                                        <label class="mt-1 mb-1 inputlabel formlabel" for="hospitalName">Hospital
                                            Name</label>
                                        <input type="text" class="form-control mt-1 inputfield" id="hospitalName"
                                            name="hospitalName" placeholder="" autofocus>
                                    </div>
                                    <div class="col-xl-1 col-lg-4 col-11 mb-3">
                                        <label class="mt-1 mb-1 inputlabel formlabel"
                                            for="startingTime">Starting</label>
                                        <input type="text" class="form-control mt-1 inputfield" id="startingTime"
                                            name="startingTime" placeholder="" autofocus>
                                    </div>
                                    <div class="col-xl-1 col-lg-4 col-11 mb-3">
                                        <label class="mt-1 mb-1 inputlabel formlabel"
                                            for="endingtime">Ending</label>
                                        <input type="text" class="form-control mt-1 inputfield" id="endingtime"
                                            name="endingtime" placeholder="" autofocus>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-12 mb-3">
                                        <label class="mt-1 mb-1 inputlabel formlabel" for="hospitalName">Hospital
                                            Address</label>
                                        <input type="text" class="form-control mt-1 inputfield" id="hospitalAddress"
                                            name="hospitalAddress" placeholder="" autofocus>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-12 mb-3">
                                        <label class="mt-1 mb-1 inputlabel formlabel" for="hospitalName">Location</label>
                                        <input type="text" class="form-control mt-1 inputfield" id="hospitalLocation"
                                            name="hospitalLocation" placeholder="" autofocus>
                                    </div>
                                    <div class="col-1 d-flex align-items-center">
                                        <span class="add-icon" onclick="addHospitalRow()"><i class="bi bi-patch-plus-fill"></i></span>
                                        <div class="tooltip" id="tooltip">Please click here</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="hospital-list">
                            <!-- Hospital rows will be added here -->
                        </div>
                        <div class="row">

                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="mobile_no">Mobile No<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <input type="number" class="form-control inputfield" id="mobile_no" name="MobileNo"
                                    placeholder="" autofocus required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="location">Location</label>
                                <input type="text" class="form-control inputfield" id="location" name="Location"
                                    placeholder="" autofocus>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="Email">Email<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <input type="email" class="form-control inputfield" id="Email" name="email_id"
                                    placeholder="" autofocus required>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="password">Password<span
                                        style="color:red; font-size:15px"> *</span></label>
                                <input type="password" class="form-control inputfield" id="password"
                                    name="password" placeholder="" autofocus required>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="enq_source">Sub Subspecification</label>
                                <select class="inputfield staffselect subspecificationselect multiselect" multiple
                                    id="show_members" name="subspecailization" required>
                                    <option hidden value="">Select Subspecification</option>
                                    @foreach ($subspecification as $key)
                                        <option class="inputlabel" value="{{ $key->id }}">
                                            {{ $key->subspecification }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel" for="enq_source">Specification</label>
                                <select class="inputfield staffselect specificationselect multiselect" multiple
                                    id="specification" name="specification" autofocus>
                                    <option hidden class="inputlabel" value="">Select Specification</option>
                                    @foreach ($Specification as $key)
                                        <option class="inputlabel" value="{{ $key->id}}">
                                            {{ $key->specification }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel">Services At</label><br>
                                <textarea cols="30" rows="2" class="form-control inputfield LocalAddress" id="service_at"
                                    name="services" placeholder="Enter Experience"></textarea>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                <label class="mt-1 mb-1 inputlabel">About</label><br>
                                <textarea cols="30" rows="2" class="form-control inputfield LocalAddress" id="about" name="About"
                                    placeholder="Enter Local Address"></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn responsebtn px-5">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        </div>



    </main><!-- End #main -->
    @include('footer')

    <script>
        //image upload preview
        document.getElementById('profile_picture').addEventListener('change', function(event) {
            const fileInput = event.target;
            const imagePreview = document.getElementById('imagePreview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.style.backgroundImage = `url(${e.target.result})`;
                    imagePreview.style.backgroundSize = 'cover';
                    imagePreview.style.width = '100px';
                    imagePreview.style.height = '100px';
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        });
        $('.profile_picture').on('change', function() {
            var selectedImage = this.files[0];
            if (selectedImage) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.profile-image-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(selectedImage);
            }
        });

        const hospitals = [];

        function addHospitalRow() {
            const hospitalNameInput = document.getElementById("hospitalName");
            const StartingTimeInput = document.getElementById("startingTime");
            const EndingTimeInput = document.getElementById("endingtime");
            const hospitalAddressInput = document.getElementById("hospitalAddress");
            const LocationInput = document.getElementById("hospitalLocation");

            const hospitalName = hospitalNameInput.value;
            const startingTime = StartingTimeInput.value;
            const endingTime = EndingTimeInput.value;
            const hospitalAddress = hospitalAddressInput.value;
            const hospitalLocation = LocationInput.value;

            if (hospitalName && startingTime) {
                // Create a new row with the entered data
                const newRow = document.createElement("div");
                newRow.classList.add("col-xl-12", "col-lg-12", "col-12", "mb-3", "hospital-row");

                newRow.innerHTML = `
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-12 mb-3">
                    <label class="mt-1 mb-1 inputlabel formlabel">Hospital Name</label>
                    <input type="text" class="form-control mt-1 inputfield" value="${hospitalName}" readonly>
                </div>
                <div class="col-xl-1 col-lg-4 col-12 mb-3">
                    <label class="mt-1 mb-1 inputlabel formlabel">starting</label>
                    <input type="text" class="form-control mt-1 inputfield" value="${startingTime}" readonly>
                </div>
                <div class="col-xl-1 col-lg-4 col-12 mb-3">
                    <label class="mt-1 mb-1 inputlabel formlabel">Ending</label>
                    <input type="text" class="form-control mt-1 inputfield" value="${endingTime}" readonly>
                </div>
                <div class="col-xl-3 col-lg-4 col-12 mb-3">
                    <label class="mt-1 mb-1 inputlabel formlabel">Hospital Address</label>
                    <input type="text" class="form-control mt-1 inputfield" value="${hospitalAddress}" readonly>
                </div>
                <div class="col-xl-3 col-lg-5 col-11 mb-3">
                    <label class="mt-1 mb-1 inputlabel formlabel">Location</label>
                    <input type="text" class="form-control mt-1 inputfield" value="${hospitalLocation}" readonly>
                </div>
            </div>
        `;

                // Add the new row to the hospital list
                document.getElementById("hospital-list").appendChild(newRow);

                // Push the data into the hospitals array
                hospitals.push({
                    hospitalName,
                    startingTime,
                    endingTime,
                    hospitalAddress,
                    hospitalLocation

                });
                console.log(hospitals)
                // Clear the input fields
                hospitalNameInput.value = "";
                StartingTimeInput.value = "";
                EndingTimeInput.value = "";
                hospitalAddressInput.value = "";
                hospitalLocation.value = "";
            }
        }

        var selectize = $('#show_members')[0].selectize;



        $("#Enquiry_form").validate({
            rules: {
                GroupName: {
                    required: true,
                    minlength: 2,
                    maxlength: 25,
                },
                branchID: {
                    required: true,
                }
            },
            messages: {
                GroupName: {
                    required: "This field is required",
                    minlength: "atleast 2 characters",
                    maxlength: "maximum 25 characters",
                },
                branchID: {
                    required: "Please select Branch",
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "branchID") {
                    // Place the error message in the "branchError" element
                    error.appendTo("#branchError");
                } else {
                    // Use the default placement for other elements
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                var FirstName = $('#first_name').val();
                var SecondName = $('#second_name').val();
                var MobileNumber = $('#mobile_no').val();
                var Location = $('#location').val();
                var Gender = $('#gender').val();
                var Specialization = $('#specialization').val();
                var EmailId = $('#Email').val();
                var password = $('#password').val();
                var specification = $('#specification').val().join(',');
                var Subspecification = $('#show_members').val().join(',');
                var selectize = $('.subspecificationselect')[0].selectize;
                var selectize1 = $('.specificationselect ')[0].selectize;
                var About = $('#about').val();
                var ServiceAt = $('#service_at').val();
                // Create a FormData object to handle the file upload
                var formData = new FormData();
                formData.append('firstname', FirstName);
                formData.append('location', Location);
                formData.append('secondname', SecondName);
                formData.append('mobileNo', MobileNumber);
                formData.append('gender', Gender);
                formData.append('email', EmailId);
                formData.append('password', password);
                formData.append('specification_id', specification);
                formData.append('subspecification_id', Subspecification);
                formData.append('specialization_id', Specialization);
                formData.append('about', About);
                formData.append('service_at', ServiceAt);
                formData.append('hospitals', JSON.stringify(hospitals));
                var profilePictureInput = $("#profile_picture")[0];
                if (profilePictureInput.files.length > 0) {
                    formData.append('docter_image', profilePictureInput.files[0]);
                }

                $.ajax({
                    url: "/api/docter",
                    method: "POST",
                    timeout: 0,
                    headers: {
                        "Accept": "application/json",
                    },
                    data: formData,
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Set content type to false as FormData handles it

                    beforeSend: function() {
                        $('.loader').show();
                        $('#groupModal').modal('hide');
                        $('.mainContents').hide();
                        $('#ResponseImage').html("");
                        $('#ResponseText').text("");
                    },
                }).done(function(response) {
                    $('.mainContents').show();
                    $('.loader').hide();
                    selectize.clear();
                    selectize1.clear();

                    console.log(response);
                    var GroupResult = JSON.stringify(response);
                    console.log(GroupResult);
                    var GroupResultObj = JSON.parse(GroupResult);
                    if (GroupResultObj.success == true) {
                        if (GroupResultObj.code == "0") {
                            swal("Warning", response.message, "warning");

                        } else if (GroupResultObj.code == "1") {
                            swal("Success", response.message, "success");

                        } else if (GroupResultObj.code == "2") {
                            swal("Error", response.message, "error");

                        }
                    } else {
                        swal("Some Error Occured!!!", "Please Try Again", "error");

                    }
                });
            }
        });
    </script>
</body>



</html>
