<!DOCTYPE html>
<html lang="en">

<head>
    @include('header')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
</head>

<body>



    <!-- Modal -->
    <div class="modal fade" id="EnquiryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Categories</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="Enquiry AddForm" id="homebanner" enctype="multipart/form-data" novalidate>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">

                                    <label class="mt-2 mb-1 inputlabel">Category Name:<span
                                            style="color:red; font-size:15px">*</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="category_name"
                                        name="category_name" pattern="[^0-9]+" placeholder="Enter description" autofocus
                                        required>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2 mb-1 inputlabel">Description:<span
                                            style="color:red; font-size:15px">*</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="description"
                                        name="description" pattern="[^0-9]+" placeholder="Enter description" autofocus
                                        required>
                                </div>
                            </div>
                            {{-- <label class="mb-1">{{ __('label.image') }}</label>
                            <input type="file" onchange="previewImages(this)" id="fileupload" class="" hidden
                                name="image" multiple />
                            <label for="fileupload" class="form-control file-control">
                                <span>Select Image(s)</span>
                            </label> --}}
                            {{-- <label for="image">Image:</label>
                            <input type="file" name="image" accept="image/*"> --}}
                            <div class="col-xl-6 col-lg-6 col-6">
                                <label class="mt-2 mb-1 inputlabel">Profile Picture</label><br>
                                <input type="file" class="form-control mt-1" id="profile_picture" name="image"
                                    accept="image/*">
                            </div>
                            <div class="form-group">
                                <label class="mt-2 mb-1 inputlabel">Type:<span
                                        style="color:red; font-size:15px">*</span></label><br>
                                <select class="form-control mt-1 inputfield" id="type" name="type" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="medicine">Medicine</option>
                                </select>
                            </div>
                            <div class="thumbnail-img" id="idMainLogo"></div>
                            <div class="form-group">
                                <label for="doctorsList">Select Doctor:</label>
                                <select class="form-control" id="doctorsList" name="doctorsList"></select>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn savebtn px-5">Save</button>
                            </div>
                        </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <div class="wrapper">
        <!--CONTENTS-->
        <div class="container-fluid mainContents">
            <div class="card card-body main_card mt-2 shadow-lg p-3 mb-2">

                <div class=" row main_heading  mb-2 shadow p-2 subheading">
                    <div id="exportbtns" class="exportbtns col-md-4 col-12">
                        <!-- export buttons -->
                    </div>
                    <div class="col-md-4 col-12">
                        <input type="text" class="form-control text-center" id="SearchBar" placeholder="Search">
                    </div>
                    <div class="col-md-4 col-12 text-end">
                        <button class="btn AddBtn px-4" data-bs-toggle="modal" data-bs-target="#EnquiryModal">+
                            Add</button>

                    </div>
                </div>

                <div class="admintoolbar">

                </div>
                <div class="table-responsive tablelapView">
                    <table class="table  table-hover MasterTable" id="MasterTable" style="width: 100%;">
                        <thead class=" tablehead">
                            <tr>
                                <th class="text-center">Sl No</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>


    @include('footer')


    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var selectedDoctorsArray = [];

        $('.MasterTable thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('.MasterTable thead');
        var MasterTable = $('.MasterTable').DataTable({
            processing: true,
            orderCellsTop: true,
            fixedHeader: true,
            "dom": 'Blrt<"bottom"ip>',
            "pagingType": 'simple_numbers',
            "language": {
                "paginate": {
                    "previous": "<i class='bi bi-caret-left-fill'></i>",
                    "next": "<i class='bi bi-caret-right-fill'></i>"
                }
            },
            buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
            ],

            initComplete: function() {
                $("#MasterTable").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                var api = this.api();
                // For each column
                api
                    .columns()
                    .eq(0)
                    .each(function(colIdx) {
                        // Set the header cell to contain the input element
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        if (colIdx < api.columns().nodes().length - 1) {
                            $(cell).html(
                                '<input type="text" class="text-center searchcolumn" placeholder="Search" />'
                            );
                        } else {
                            $(cell).empty();
                        }
                        // On every keypress in this input
                        $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                            .off('keyup change')
                            .on('change', function(e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                // var cursorPosition = this.selectionStart;
                                // Search the column for that value
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != '' ?
                                        regexr.replace('{search}', '(((' + this.value + ')))') :
                                        '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            })
                            .on('keyup', function(e) {
                                e.stopPropagation();

                                $(this).trigger('change');
                                $(this)
                                    .focus()[0]
                                // .setSelectionRange(cursorPosition, cursorPosition);
                            });
                    });
                $('.dt-buttons').appendTo('#exportbtns');
            },


            ajax: "{{ route('Categories.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                // {
                //     data: 'image',
                //     name: 'image',
                //     render: function(data, type, full, meta) {
                //         var image = '/img/' + data;
                //         return '<div class="image-rectangle" ><img src="' + image +
                //             '" alt="image" width="200px" height="60px"></div>';
                //     }
                // },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'description',
                    name: 'description'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $(document).ready(function() {
            $('#type').change(function() {
                var selectedType = $(this).val();
                var doctorsDropdown = $('#doctorsList');

                if (selectedType === 'doctor') {
                    // Fetch data through AJAX
                    $.ajax({
                        url: '/api/getalldocters',
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Show and initialize selectize with fetched data
                            doctorsDropdown.show().selectize({
                                plugins: ['remove_button'],
                                create: false,
                                persist: false,
                                maxItems: null,
                                valueField: 'id',
                                valueField1: 'UserId',
                                labelField: 'firstname',
                                searchField: ['firstname'],
                                options: data
                                    .Docters, // Assuming data.Docters is an array of doctors
                                render: {
                                    option: function(item, escape) {
                                        return '<div>' +
                                            '<span class="title">' + escape(item
                                                .firstname) + '</span>' +
                                            '</div>';
                                    }
                                },
                                onChange: function(value) {
                                    // Clear the existing array
                                    selectedDoctorsArray = [];

                                    // Populate the array with selected doctors
                                    value.forEach(function(doctorId) {
                                        var doctor = data.Docters.find(
                                            function(d) {
                                                return d.id == doctorId;
                                            });

                                        if (doctor) {
                                            selectedDoctorsArray.push({
                                                id: doctor.id,
                                                name: doctor
                                                    .firstname,
                                                userid: doctor.UserId,
                                            });
                                        }
                                    });

                                    console.log(selectedDoctorsArray);
                                }
                            });
                        },

                        error: function(error) {
                            console.error('Error fetching doctors:', error);
                        }
                    });
                } else {
                    // Hide and destroy selectize
                    var selectize = doctorsDropdown[0].selectize;
                    if (selectize) {
                        selectize.destroy();
                    }
                    doctorsDropdown.hide();
                }
            });
        });
        // Initialize validation for the form




        $("#homebanner").validate({
            submitHandler: function(form, e) {
                e.preventDefault(); // Add parentheses to invoke the function

                var catogoryName = $('#category_name').val();
                var DataType = $('#type').val();
                var Description = $('#description').val();
                let DoctorList = JSON.stringify(selectedDoctorsArray);

                // Create a FormData object to handle the file upload
                var formData = new FormData();
                formData.append('category_name', catogoryName);
                formData.append('description', Description);
                formData.append('type', DataType);
                formData.append('doctorsList', DoctorList);

                var profilePictureInput = $("#profile_picture")[0];
                if (profilePictureInput.files.length > 0) {
                    formData.append('image', profilePictureInput.files[0]);
                }

                $.ajax({
                    url: "/api/Categories",
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
                        $('#EnquiryModal').modal('hide');
                        $('.mainContents').hide();
                        $('#ResponseImage').html("");
                        $('#ResponseText').text("");
                    },
                }).done(function(response) {
                    $('.mainContents').show();
                    $('.loader').hide();

                    console.log(response);
                    var EnquiryResult = JSON.stringify(response);
                    console.log(EnquiryResult);
                    var EnquiryResultObj = JSON.parse(EnquiryResult);

                    if (EnquiryResultObj.success == true) {
                        if (EnquiryResultObj.code == "0") {
                            swal("Warning", response.message, "warning");
                        } else if (EnquiryResultObj.code == "1") {
                            swal("Success", response.message, "success");
                        } else if (EnquiryResultObj.code == "2") {
                            swal("Error", response.message, "error");
                        }
                    } else {
                        swal("Some Error Occurred!!!", "Please Try Again", "error");
                    }
                });
            }
        });

           //edit Specialization
     $('#MasterTable').on('click', '.btn_edit', function() {
                var EditCategory = $(this).val();
                console.log(EditCategory);

                var settings = {
                    "url": "/api/Showcategories/" + EditCategory + "",
                    "method": "GET",
                    "timeout": 0,
                };

                $.ajax(settings).done(function(response) {
                    console.log(response);
                    var CategoryResult = JSON.stringify(response);
                    console.log(CategoryResult);
                    var Categoryedit = JSON.parse(CategoryResult);
                    if (Categoryedit.success == true) {
                        $('#EnquiryModal').modal('show');
                        $('#homebanner').hide();
                        $('#update_enquiry').show();
                        var CoTypeDataArray = Categoryedit.Category;
                        for (const key in CoTypeDataArray) {
                            var CategoryId = CoTypeDataArray['id'];
                            var CategoryName = CoTypeDataArray['Category_name'];
                            var Description =  CoTypeDataArray['Description'];
                        }
                        $("#update_enquiry_id").val(CategoryId);
                        $("#update_enquiry_name").val(CategoryName);
                        $("#update_enquiry_type").val(Categorytype);
                        $("#update_enquiry_desc").val(Description);


                    }
                });



            });

        //Delete code of ajax
        $('#MasterTable').on('click', '.btn_delete', function() {
            var DeleteCategory = $(this)
                .val(); //this is usually used as a click event .The result of $(this).val() is assigned to the variable DeleteSymptom
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "/api/Categories/" + DeleteCategory,
                        method: "DELETE",
                        timeout: 0,
                        headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        beforeSend: function() {
                            $('.loader').show();
                            $('.mainContents').hide();
                        },
                    }).done(function(response) {
                        $('.mainContents').show();
                        $('.loader').hide();
                        MasterTable.ajax.reload();
                        console.log(response);
                        console.log(response.message);
                        var CategoryResult = JSON.stringify(response);
                        console.log(CategoryResult);
                        var SpecializtionObj = JSON.parse(CategoryResult);
                        if (SpecializtionObj.success == true) {
                            if (SpecializtionObj.code == "0") {

                                swal("Warning", response.message, "warning");
                            } else if (SpecializtionObj.code == "1") {

                                swal("Success", response.message, "success");
                            } else if (SpecializtionObj.code == "2") {

                                swal("Error", response.message, "error");
                            }
                        } else {
                            // Error icon
                            swal("Some Error Occurred!!!", "Please Try Again", "error");
                        }
                    });
                }
            })
        });
    </script>


</body>

</html>
