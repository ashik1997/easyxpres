<!--
Developer info:
=================
|---------------------------------------------------------|
| Name   | Ashik                                          |
| Skype  | ashikur551                                     |
| Phone  | +8801731002123                                 |
| Email  | ashikurashik.sc@gmail.com                      |
|---------------------------------------------------------|
-->

<!DOCTYPE html>
<html lang="en">
<meta
    http-equiv="content-type"
    content="text/html;charset=utf-8"
/>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        http-equiv="x-ua-compatible"
        content="ie=edge"
    >

    <title>Admin | @yield('title')</title>
    <!-- favicon link-->

    @yield('link')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript">
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    </script>
    <style type="text/css">
        .table thead tr th,
        tfoot tr th:first-letter {
            text-transform: capitalize;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .content-header h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .content-header {
            padding: 4px .5rem;
        }

        .card-header>.card-tools {
            float: right;
            margin-right: 0;
        }

        /* Media query for mobile devices */
        @media (max-width: 767px) {
            .content-header .col-sm-6 {
                width: 50%;
                /* Set width to 50% on mobile devices */
            }

            .content-header .breadcrumb {
                float: right;
            }
        }
    </style>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
    >
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

</head>

<body class="hold-transition sidebar-mini">
    <div id="app">
        @if (Session::has('flash_success'))
            {!! session('flash_success') !!}
        @endif
        <div class="wrapper">
            @include('admin.inc.navbar')
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
            <!-- content-wrapper -->

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- control-sidebar -->

        </div>
        <!-- wrapper -->
    </div>
    <!-- REQUIRED SCRIPTS -->

    @yield('script')
    <!-- Sidebar Search HTML -->

    <script>
        function filterSidebarMenu() {
            const query = document.getElementById('sidebar-search').value.toLowerCase();
            const menuItems = document.querySelectorAll('#sidebar-menu > .nav-item');

            menuItems.forEach(item => {
                const menuText = item.querySelector('.nav-link p').textContent.toLowerCase();
                const childItems = item.querySelectorAll('.nav-treeview .nav-item');

                let isMatch = menuText.includes(query);

                childItems.forEach(child => {
                    const childText = child.querySelector('.nav-link p').textContent.toLowerCase();
                    if (childText.includes(query)) {
                        isMatch = true;
                        child.style.display = '';
                    } else {
                        child.style.display = 'none';
                    }
                });

                item.style.display = isMatch ? '' : 'none';
            });
        }

        $(document).ready(function() {
            $('#invoice-search-form').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                const invoiceId = $('#invoice-id').val().trim(); // Get the entered Invoice ID
                if (!invoiceId) {
                    toastr.warning('Please enter an invoice ID.');
                    return;
                }

                // Dynamically create the API URL


                // Make an AJAX request
                $.ajax({
                    url: invoiceShowForPrintUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response && Object.keys(response).length > 0) {
                            $('.content-wrapper').html(
                                response); // Populate the #app div with the response
                        } else {
                            toastr.warning('No invoice data found for the provided ID.');
                            // $('.content-wrapper').html(''); // Clear the #app div
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 404) {
                            toastr.warning(
                                'Error 404: Invoice not found. Please check the invoice ID and try again.'
                            );
                        } else {
                            toastr.error(
                                'Error: Unable to retrieve the invoice. Please try again later.'
                            );
                        }
                        // $('.content-wrapper').html(''); // Clear the #app div in case of error
                    }
                });
            });
        });
    </script>

    <script type="text/javascript">
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        function copyText(id) {
            // Get the text field
            var copyText = document.getElementById(id);

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);
            toastr.success('Copied to Clipboard');
            // Alert the copied text
            // alert("Copied the text: " + copyText.value);
        }

        function printDiv1(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }

        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            // Replace body content with div content
            document.body.innerHTML = printContents;

            // Trigger print dialog
            window.print();

            // After printing, restore content and reload the page
            setTimeout(function() {
                document.body.innerHTML = originalContents;
                location.reload(); // reloads the current page
            }, 100);
        }

        function downloadImage(divId) {
            html2canvas(document.getElementById(divId)).then(function(canvas) {
                // Create a link element
                var link = document.createElement('a');
                link.href = canvas.toDataURL(); // Convert the canvas to a data URL
                link.download = 'request_' + divId + '.png'; // Set the file name
                link.click(); // Trigger the download
            });
        }
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default action (navigation)

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Check if it's a form or link
                        const form = button.closest('form');
                        if (form) {
                            form.submit(); // Submit the form if it's inside a form
                        } else {
                            const href = button.getAttribute('href');
                            if (href) {
                                // Redirect to the link if it's a simple <a> tag
                                window.location.href = href;
                            }
                        }
                    }
                });
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            const notificationItems = document.querySelectorAll('.dropdown-item[data-id]');
            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');

                    fetch(`/notifications/${notificationId}/read`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                        .then(response => response.json()) // Try parsing as JSON
                        .then(data => {
                            if (data.status === 'success') {
                                // Update the UI
                                const notificationElement = document.querySelector(
                                    `#notification-${notificationId}`);
                                notificationElement.classList.remove('bg-danger');
                                notificationElement.classList.add('bg-light');

                                // Decrease the unread notification count
                                const unreadCountElement = document.querySelector(
                                    '.navbar-badge');

                                if (unreadCountElement) {
                                    const currentCount = parseInt(unreadCountElement
                                        .textContent, 10) || 0;
                                    unreadCountElement.textContent = Math.max(0, currentCount -
                                        1); // Prevent negative values
                                } else {
                                    console.warn('Unread count element not found.');
                                }

                            }
                        })
                        .catch(error => {
                            console.error('Error marking notification as read:', error);
                            toastr.error('There was an error. Please try again.');
                        });

                });
            });
        });
    </script>
</body>

</html>
