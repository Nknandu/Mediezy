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
                        <h6 class="modal-title" id="exampleModalLabel">Medical Prescription</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <form class="Enquiry AddForm" id="enquiry_type" novalidate>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class=" col-8">
                                    <label class="mt-2 mb-1 inputlabel">Medicine Name<span
                                            style="color:red; font-size:15px">
                                            *</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="MedicineName"
                                        name="MedicineName" pattern="[^0-9]+" placeholder="Medicine Name" autofocus
                                        required>
                                </div>
                                <div class="col-4">
                                    <label class="mt-2 mb-1 inputlabel">Dosage</label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="Dosage"
                                        name="Dosage" pattern="[^0-9]+" placeholder="Dosage" autofocus required>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <input type="text" class="form-control mt-3 inputfield" name="Days"
                                        pattern="[^0-9]+" value="DAYS" autofocus disabled>
                                </div>
                                <div class="col-6">
                                    <select class="form-select inputfield mt-3" aria-label="Default select example name"
                                        id="days" name="DAYS" autofocus>
                                        <option hidden class="inputlabel" value="0"> Choose Days</option>
                                        <option class="inputlabel" value="1"> 1</option>
                                        <option class="inputlabel" value="2"> 2</option>
                                        <option class="inputlabel" value="3"> 3</option>
                                        <option class="inputlabel" value="4"> 4</option>
                                        <option class="inputlabel" value="5"> 5</option>
                                        <option class="inputlabel" value="6"> 6</option>
                                        <option class="inputlabel" value="7"> 7</option>
                                        <option class="inputlabel" value="8"> 8</option>
                                        <option class="inputlabel" value="9"> 9</option>
                                    </select>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <input type="checkbox" value="1" id="beforeFoodMrng">Morning(B/F)

                                    </div>
                                    <div class="col-4">
                                        <input type="checkbox" value="1" id="AfterFoodMorning">Morning(A/F)

                                    </div>
                                    <div class="col-2">
                                        <input type="checkbox" value="1" id="Noon">Noon

                                    </div>
                                    <div class="col-2">
                                        <input type="checkbox" value="1" id="night">Night

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
                                    <input type="text" class="form-control mt-1 inputfield"
                                        id="update_enquiry_name" name="UpdateEnquiryName" autofocus required>
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
                            <input type="text" class="form-control text-center" id="SearchBar"
                                placeholder="Search">
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
                                    <th class="text-center">Medicine Name</th>
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


                ajax: "{{ route('medicine.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'medicineName',
                        name: 'medicineName'
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
                    var MedicineName = $('#MedicineName').val();
                    var Dosage = $('#Dosage').val();
                    var NOofDays = $('#days').val();
                    var morningBF = $('#beforeFoodMrng').is(":checked") ? 1 : 0;
                    var morningAF = $('#AfterFoodMorning').is(":checked") ? 1 : 0;
                    var Noon = $('#Noon').is(":checked") ? 1 : 0;
                    var Night = $('#night').is(":checked") ? 1 : 0;
                    $.ajax({
                        url: "/api/Medicine",
                        method: "POST",
                        timeout: 0,
                        headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        data: {
                            medicineName: MedicineName,
                            Dosage: Dosage,
                            NoOfDays: NOofDays,
                            MorningBF: morningBF,
                            MorningAF: morningAF,
                            Noon: Noon,
                            night: Night
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
                    "url": "/api/Medicine/" + EditSpecialization + "",
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
                        var CoTypeDataArray = Specializationedit.medicines;
                        for (const key in CoTypeDataArray) {
                            var MedicineName = CoTypeDataArray['specialization'];
                            var MedicineDosage = CoTypeDataArray['remark'];
                            var MedicineNoOfDays = CoTypeDataArray['remark'];
                            var MedicineMorningAF = CoTypeDataArray['remark'];
                            var MedicineMorningBF = CoTypeDataArray['id'];

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
        </script>
</body>

</html>
