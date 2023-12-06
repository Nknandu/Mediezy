<!DOCTYPE html>
<html>

<head>
    <?php echo $__env->make('header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body>

    <!-- ======= Header ======= -->

    <header id="header" class="header fixed-top">
        <?php echo $__env->make('navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </header>

    <!-- End Header -->
    <!-- ======= Sidebar ======= -->

    <aside id="sidebar" class="sidebar ps-0">
        <?php echo $__env->make('sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </aside>



    <main id="main">


        <div class="modal fade addUpdateModal" id="EnquiryModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content cntrymodalbg">
                    <div class="modal-header modelhead py-2">
                        <h6 class="modal-title" id="exampleModalLabel">Schedule Manager</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">


                    </div>
                </div>

            </div>
        </div>
        </div>
        </div>

        <div class="modal fade addUpdateModal " id="ScheduleViewModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content cntrymodalbg">
                    <div class="modal-header modelhead py-2">
                        <h6 class="modal-title" id="exampleModalLabel">Shedule Manager</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="Enquiry AddForm" id="view_add_schedule" novalidate>
                            <div class="row">
                                <div class=" col-12">
                                    <input type="hidden" class="form-control mt-1 inputfield" id="view_id"
                                        name="viewId" autofocus required>
                                    <label class="mt-2 mb-1 inputlabel">Docter<span style="color:red; font-size:15px">
                                            *</span></label><br>
                                    <select class="form-select  inputfield" aria-label="Default select example name"
                                        id="view_docter_id" name="docter_id" autofocus disabled>
                                        <option hidden class="inputlabel" value="">Choose Docter</option>
                                        <?php $__currentLoopData = $Docter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option class="inputlabel" value="<?php echo e($key->id); ?>">
                                                <?php echo e($key->firstname); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class=" col-12">
                                    <label class="mt-2 mb-1 inputlabel">Seesion Title<span
                                            style="color:red; font-size:15px">
                                            *</span></label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="view_session_name"
                                        name="EnquiryName" pattern="[^0-9]+" placeholder="Enter session Title"
                                        autofocus disabled>
                                </div>
                                <div class=" col-12">
                                    <label class="mt-3 mb-1 inputlabel">Date</label><br>
                                    <input type="date" value="<?php echo date('Y-m-d'); ?>"
                                        class="form-control mt-1 inputfield" id="view_date" name="date"
                                        pattern="[^0-9]+" min="<?php echo date('Y-m-d'); ?>" autofocus disabled>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label class="mt-3 mb-1 inputlabel">Starting Time</label><br>
                                    <input type="time" class="form-control mt-1 inputfield"
                                        id="view_starttingtime" name="starttingtime" pattern="[^0-9]+" autofocus
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label class="mt-3 mb-1 inputlabel">Ending Time</label><br>
                                    <input type="time" class="form-control mt-1 inputfield" id="view_endingtime"
                                        name="endingtime" pattern="[^0-9]+" autofocus disabled>
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="mt-3 mb-1 inputlabel">Time Duration</label><br>
                                            <input type="text" class="form-control mt-1 inputfield"
                                                id="view_timeduration" name="timeduration" pattern="[^0-9]+"
                                                autofocus disabled>
                                        </div>
                                        <div class="col-6">
                                            <label class="mt-3 mb-1 inputlabel">Format<span
                                                    style="color:red; font-size:15px">
                                                    *</span></label><br>
                                            <input type="text" class="form-control mt-1 inputfield"
                                                id="view_format" name="view_format" pattern="[^0-9]+" autofocus
                                                disabled>


                                            </select>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-12">
                                    <label class="mt-3 mb-1 inputlabel">Number Of Token</label><br>
                                    <input type="text" class="form-control mt-1 inputfield" id="view_Token"
                                        name="token" pattern="[^0-9]+" autofocus disabled>
                                </div>
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
                            <a class="btn AddBtn px-4" href="<?php echo e(url('/Tokengeneration')); ?>">+
                                Add</a>

                        </div>
                    </div>

                    <div class="admintoolbar">

                    </div>
                    <div class="table-responsive tablelapView">
                        <table class="table  table-hover MasterTable" id="MasterTable" style="width: 100%;">
                            <thead class=" tablehead">
                                <tr>
                                    <th class="text-center">Sl No</th>
                                    <th class="text-center">Docter</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Number Of Token</th>
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
        <?php echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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


                ajax: "<?php echo e(route('schedulemanager.index')); ?>",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'DocterName',
                        name: 'DocterName'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'TokenCount',
                        name: 'TokenCount'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });




            //edit Specialization
            $('#MasterTable').on('click', '.btn_edit', function() {
                var EditSpecialization = $(this).val();
                console.log(EditSpecialization);

                var settings = {
                    "url": "/api/schedule/" + EditSpecialization + "",
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
                        var CoTypeDataArray = Specializationedit.Specification;
                        for (const key in CoTypeDataArray) {
                            var SpecializationName = CoTypeDataArray['specification'];
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

                        url: "/api/specialization/" + UpdateId + "",
                        method: "PUT",
                        timeout: 0,
                        headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        data: {
                            specification: UpdateSpecialization,
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

                var DeleteScedule = $(this).val();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "/api/schedule/" + DeleteScedule,
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

            //View Enquiry Type
            $('#MasterTable').on('click', '.btn_view', function() {
                var ViewEnType = $(this).val();
                console.log(ViewEnType);
                var settings = {
                    "url": "/api/schedule/" + ViewEnType + "",
                    "method": "GET",
                    "timeout": 0,
                };

                $.ajax(settings).done(function(response) {
                    console.log(response);
                    var EnResult = JSON.stringify(response);
                    console.log(EnResult);
                    var Enedit = JSON.parse(EnResult);
                    if (Enedit.success == true) {
                        $('#ScheduleViewModal').modal('show');
                        $('#view_add_schedule').show();
                        var EnDataArray = Enedit.specialization;
                        for (const key in EnDataArray) {
                            var DocterName = EnDataArray['docter_id'];
                            var sessionTitile = EnDataArray['session_title'];
                            var Date = EnDataArray['date'];
                            var StartingTime = EnDataArray['startingTime'];
                            var EndingTime = EnDataArray['endingTime'];
                            var TotalToken = EnDataArray['token'];
                            var TimeDuration = EnDataArray['timeduration'];
                            var Formate = EnDataArray['format'];
                            var ScheduleId = EnDataArray['id'];


                        }
                        $("#view_id").val(ScheduleId);
                        $("#view_docter_id").val(DocterName);
                        $("#view_session_name").val(sessionTitile);
                        $("#view_date").val(Date);
                        $("#view_starttingtime").val(StartingTime);
                        $("#view_endingtime").val(EndingTime);
                        $("#view_Token").val(TotalToken);
                        $("#view_timeduration").val(TimeDuration);
                        $("#view_format").val(Formate);

                    }
                });

            });



        </script>
</body>

</html>
<?php /**PATH /home/1163996.cloudwaysapps.com/cdeuzgpxfr/public_html/resources/views/Schedule/index.blade.php ENDPATH**/ ?>