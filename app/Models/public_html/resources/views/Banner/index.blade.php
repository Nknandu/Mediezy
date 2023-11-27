<!DOCTYPE html>
<html>

<head>
    @include('header')
</head>
<style>
    .thumbnail-img img {
        width: 225px;
        height: 225px;
    }

    .image-rectangle {
        width: 100px;
        /* Set your desired width */
        height: 60px;
        /* Set your desired height */
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



    <main id="main">


        <div class="modal fade addUpdateModal" id="EnquiryModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content cntrymodalbg">
                    <div class="modal-header modelhead py-2">
                        <h6 class="modal-title" id="exampleModalLabel">Schedule Manager</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>
                    <div class="modal-body">
                        <form class="Enquiry AddForm" id="homebanner" enctype="multipart/form-data" novalidate>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="mb-1">{{ __('label.image') }}</label>
                                        <input type="file" onchange="previewImages(this)" id="fileupload"
                                            class="" hidden name="profile_img[]" multiple />
                                        <label for="fileupload" class="form-control file-control">
                                            <span>Select Image(s)</span>
                                        </label>
                                        <div class="thumbnail-img" id="idMainLogo">

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn savebtn px-5">Save</button>
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
                                    <th class="text-center">IMAGE</th>
                                    <th class="text-center">First Image</th>
                                    <th class="text-center">Footer Image</th>
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


            ajax: "{{ route('bannerImage.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'bannerImage',
                    name: 'bannerImage',
                    render: function(data, type, full, meta) {
                        // Assuming 'docter_image' contains the image file name
                        var imageUrl = '/BannerImages/images/' + data;
                        // Use CSS to style the image as round
                        return '<div class="image-rectangle" ><img src="' + imageUrl +
                            '" alt="Banner Image" width="200px" height="60px"></div>';
                    }
                },


                {
                    data: 'firstImage',
                    name: 'firstImage'
                },
                {
                    data: 'footerImage',
                    name: 'footerImage'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });



        $("#homebanner").validate({
            rules: {
                // Add the customEmail rule to the email field


                StaffPassword: {
                    required: true,

                },




            },
            messages: {

                ConfirmPassword: {
                    required: "Please confirm your password",
                    equalToPassword: "Passwords do not match", // Custom error message when passwords don't match
                },
            },
            submitHandler: function(form) {

                var formData = new FormData(form);

                $.ajax({
                    url: "/api/banner",
                    method: "POST",
                    timeout: 0,
                    headers: {
                        "Accept": "application/json"
                    },
                    processData: false,
                    contentType: false,
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

                    console.log(response);

                    if (response.success) {
                        if (response.code == "0") {
                            swal("Warning", response.message, "warning");
                        } else if (response.code == "1") {
                            swal("Success", response.message, "success");
                        } else if (response.code == "2") {
                            swal("Error", response.message, "error");
                        } else if (response.code == "3") {
                            swal("Error", response.message, "error");
                        }
                    } else {
                        if (response.error && response.error.email) {
                            swal("Error", response.error.email[0], "error");
                        } else {
                            swal("Some Error Occurred!!!", "Please Try Again", "error");
                        }
                    }
                });
            }
        });

        function previewImages(input) {
            var previewContainer = document.getElementById('idMainLogo');
            previewContainer.innerHTML = ''; // Clear previous previews

            var files = input.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();

                reader.onload = function(e) {
                    // Create a new image element for each preview
                    var img = document.createElement('img');
                    img.src = e.target.result;

                    // Add the image to the container
                    previewContainer.appendChild(img);
                };

                reader.readAsDataURL(file);
            }
        }


        function setAsFirstImage(element) {
            if (element.checked) {
                // Uncheck all other "first image" radio buttons
                document.querySelectorAll('input[name="firstImage"]').forEach(function(radio) {
                    if (radio !== element) {
                        radio.checked = false;
                    }
                });
                // Get the image ID
                var imageId = element.getAttribute('data-id');

                // Send an AJAX request to set the image as the first image
                $.ajax({
                    url: '/api/set-first-image/' + imageId,
                    type: 'POST',
                    data: {},
                    success: function(data) {
                        console.log(data); // Handle the success response as needed
                    },
                    error: function(error) {
                        console.error(error); // Handle the error as needed
                    }
                });
            }
        }


        function setAsFooterImage(checkbox) {
        // Count the number of images with footerImage set to 1
        var checkedFooterImages = $("input[name='footerImage']:checked");

        // If there are already 3 images with footerImage set to 1, prevent changing the checkbox
        if (checkedFooterImages.length >= 3 && !checkbox.checked) {
            checkbox.checked = false;
            alert("You can only set 3 images as footer images.");
        }
        else {
            // Send an AJAX request to update the footerImage value in the database
            var imageId = $(checkbox).data('id');
            var newValue = checkbox.checked ? 1 : 0;

            $.ajax({
                type: 'PUT',
                url: '/api/update-footer/' + imageId,
                data: {
                    newValue: newValue
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
    }
    </script>
</body>

</html>
