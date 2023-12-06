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


        <div class="modal fade addUpdateModal" id="EnquiryModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content cntrymodalbg">
                    <div class="modal-header modelhead py-2">
                        <h6 class="modal-title" id="exampleModalLabel">specialization</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <form class="Enquiry AddForm" id="enquiry_type" novalidate enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class=" col-12">
                                    <label class="mt-2 mb-1 inputlabel"> Image</label><br>
                                    <input type="file" class="form-control mt-1" id="specialization_image"
                                        name="specialization_image" accept="image/*">
                                </div>
                                <div class=" col-12">
                                    <label class="mt-2 mb-1 inputlabel">Name<span style="color:red; font-size:15px">
                                            *</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="enquiry_name"
                                        name="EnquiryName" pattern="[^0-9]+" placeholder="Enter Specialization"
                                        autofocus required>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-12 mb-3">
                                    <label class="mt-2 mb-1 inputlabel"> Symtoms</label><br>
                                    <input type="text" class="form-control mt-1 inputfield symtomslist"
                                        id="symtoms" name="symtomslist" placeholder="Enter Symtoms">

                                    <div class="text-center">
                                        <button type="button" class="btn savebtn px-3 mt-2 text-center"
                                            onclick="addSymptom()">Add</button>
                                    </div>
                                </div>


                                <div class="table-responsive col-xl-6 col-lg-6 col-12 mt-4">

                                    <div class="fixTableHead" style="max-height: 90px; overflow-y: scroll;">

                                        <table class="table-hover MasterTable_inside" id="MasterTable_inside"
                                            style="width: 100%;">
                                            <thead class=" tablehead">
                                                <tr>
                                                    <th class="text-center">Sl No</th>
                                                    <th class="text-center" id="symtoms_lists">Symtoms</th>
                                                    <th class="text-center">Delete</th>

                                                </tr>
                                            </thead>
                                            <tbody id="addtaskChecklistBody">

                                            </tbody>

                                        </table>
                                    </div>
                                </div>




                            </div>
                            <div class=" text-end mt-3">
                                <button type="submit" class="btn savebtn  px-5 ">Save</button>
                            </div>
                        </form>
                        <form class="UpdateEnquiry UpdateForm" id="update_enquiry" style="display: none;" novalidate>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class=" col-12">
                                    <input type="hidden" id="update_enquiry_id">
                                    <label class="mt-2 mb-1 inputlabel">Name<span style="color:red; font-size:15px">
                                            *</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="update_enquiry_name"
                                        name="UpdateEnquiryName" autofocus required>
                                </div>

                                <input type="hidden" id="updated_by" value="0">
                            </div>
                            <div class=" text-end mt-3">
                                <button type="submit" class="btn savebtn  px-5 ">Update</button>
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
                                    <th class="text-center">specialization</th>
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
        <main id="main">

        </main>
        <!-- End Sidebar-->
        @include('footer')
        <script>
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


                ajax: "{{ route('specialize.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'specialization',
                        name: 'specialization'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $("#enquiry_type").validate({
                rules: {
                    EnquiryName: {
                        required: true,
                        minlength: 2,
                        maxlength: 35,
                    },
                    specialization_image: {
                        required: true,
                        // Add any additional rules for file upload if needed
                    }
                },
                messages: {
                    EnquiryName: {
                        required: "This field is required",
                        minlength: "At least 2 characters",
                        maxlength: "Maximum 35 characters",
                    },
                    specialization_image: {
                        required: "Please select an image",
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var specialization = $('#enquiry_name').val();
                    var symtomsData = JSON.stringify(symptomsArray);

                    // Create FormData object to handle file upload
                    var formData = new FormData(form);
                    formData.append('EnquiryName', specialization);
                formData.append('symtomslist', JSON.stringify(symptomsArray));
                var profilePictureInput = $("#specialization_image")[0];
                if (profilePictureInput.files.length > 0) {
                    formData.append('specialization_image', profilePictureInput.files[0]);
                }
                    $.ajax({
                        url: "/api/specialize",
                        method: "POST",
                        processData: false, // Prevents jQuery from automatically transforming the data
                        contentType: false, // Prevents jQuery from automatically setting the content type
                        data: formData,
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
                        MasterTable.ajax.reload();
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




            //edit Specialization
            $('#MasterTable').on('click', '.btn_edit', function() {
                var EditSpecialization = $(this).val();
                console.log(EditSpecialization);

                var settings = {
                    "url": "/api/specialize/" + EditSpecialization + "",
                    "method": "GET",
                    "timeout": 0,
                };

                $.ajax(settings).done(function(response) {
                    console.log(response);
                    var SpecializationResult = JSON.stringify(response);
                    console.log(SpecializationResult);
                    var Specializationedit = JSON.parse(SpecializationResult);
                    if (Specializationedit.success == true) {
                        $('#EnquiryModal').modal('show');
                        $('#enquiry_type').hide();
                        $('#update_enquiry').show();
                        var CoTypeDataArray = Specializationedit.specialization;
                        for (const key in CoTypeDataArray) {
                            var SpecializationName = CoTypeDataArray['specialization'];
                            var SpecializationRemark = CoTypeDataArray['remark'];
                            var SpecializationId = CoTypeDataArray['id'];

                        }
                        $("#update_enquiry_id").val(SpecializationId);
                        $("#update_enquiry_name").val(SpecializationName);
                        $("#update_remarks").val(SpecializationRemark);
                    }
                });



            });



            //Update Enquiry Type
            $("#update_enquiry").validate({
                rules: {
                    UpdateEnquiryName: {
                        required: true,
                        minlength: 2,
                        maxlength: 15,
                    }
                },
                messages: {
                    UpdateEnquiryName: {
                        required: "This field is required",
                        minlength: "atleast 2 characters",
                        maxlength: "maximum 15 characters",
                    }
                },
                submitHandler: function(form) {
                    var UpdateId = $('#update_enquiry_id').val();
                    var UpdateSpecialization = $('#update_enquiry_name').val();
                    var UpdateRemark = $('#update_remarks').val();
                    var UpdatedDy = $('#updated_by').val();


                    $.ajax({

                        url: "/api/specialize/" + UpdateId + "",
                        method: "PUT",
                        timeout: 0,
                        headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        data: {
                            specialization: UpdateSpecialization,
                            remark: UpdateRemark,


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
                        MasterTable.ajax.reload();
                        console.log(response);
                        console.log(response.message);
                        var SpecializtionResult = JSON.stringify(response);
                        console.log(SpecializtionResult);
                        var SpecializtionObj = JSON.parse(SpecializtionResult);
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
            });
            $('#MasterTable').on('click', '.btn_delete', function() {

                var DeleteSpecialization = $(this).val();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "/api/specialize/" + DeleteSpecialization,
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
                            var SpecializtionResult = JSON.stringify(response);
                            console.log(SpecializtionResult);
                            var SpecializtionObj = JSON.parse(SpecializtionResult);
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




            let symptomsArray = [];

            function addSymptom() {
                let symptomInput = document.getElementById('symtoms');
                let symptom = symptomInput.value.trim();

                if (symptom !== '') {
                    symptomsArray.push(symptom);

                    updateSymptomsTable();
                    symptomInput.value = '';
                    console.log(symptomsArray);
                }
            }

            function deleteSymptom(index) {
                symptomsArray.splice(index, 1);
                updateSymptomsTable();
                console.log(symptomsArray);
            }

            function updateSymptomsTable() {
                let tableBody = document.getElementById('addtaskChecklistBody');
                tableBody.innerHTML = '';

                symptomsArray.forEach((symptom, index) => {
                    let row = `<tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">${symptom}</td>
                    <td class="text-center"><button  class="delete btn btn_delete" onclick="deleteSymptom(${index})"> <i class="ri-delete-bin-6-line"></i></button></td>
                            </tr>`;
                    tableBody.innerHTML += row;
                });
            }
        </script>
</body>

</html>
