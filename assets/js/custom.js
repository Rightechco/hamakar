// public/assets/js/custom.js

$(document).ready(function() {
    // --- Global Flash Message Handling ---
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);

    // --- Sidebar Toggle for Mobile ---
    $('#sidebarToggle').on('click', function() {
        $('.sidebar').toggleClass('active');
        $('#sidebar-overlay').toggleClass('active');
    });

    // بستن سایدبار با کلیک روی overlay
    $('#sidebar-overlay').on('click', function() {
        $('.sidebar').removeClass('active');
        $('#sidebar-overlay').removeClass('active');
    });

    // --- Confirmation Modal Setup ---
    $('[data-confirm-delete]').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');

        $('#confirmModalLabel').text('تأیید حذف');
        $('#confirmModal .modal-body').text('آیا مطمئن هستید که می‌خواهید این مورد را حذف کنید؟ این عمل غیرقابل بازگشت است.');
        $('#confirmActionButton').removeClass('btn-primary').addClass('btn-danger').text('بله، حذف کن');

        $('#confirmActionButton').off('click').on('click', function() {
            form.submit();
            $('#confirmModal').modal('hide');
        });

        $('#confirmModal').modal('show');
    });

    // --- Generic AJAX Modal for Edit/View (Example for User Edit) ---
    $('.edit-user-btn').on('click', function() {
        var userId = $(this).data('id');
        $.ajax({
            url: APP_URL + '/ajax/get_user?id=' + userId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var user = response.data;
                    $('#editUserModalLabel').text('ویرایش کاربر: ' + user.name);
                    $('#editUserForm input[name="id"]').val(user.id);
                    $('#editUserForm input[name="name"]').val(user.name);
                    $('#editUserForm input[name="email"]').val(user.email);
                    $('#editUserForm select[name="role"]').val(user.role);
                    $('#editUserForm select[name="status"]').val(user.status);
                    $('#editUserForm').attr('action', APP_URL + '/index.php?page=admin&action=users_update&id=' + user.id);
                    $('#editUserModal').modal('show');
                } else {
                    alert('خطا در بارگذاری اطلاعات کاربر: ' + response.message);
                }
            },
            error: function() {
                alert('خطا در ارتباط با سرور.');
            }
        });
    });

    // *** منطق فرم پویا برای کارفرمای حقیقی/حقوقی ***
    function toggleClientTypeFields() {
        var userType = $('#user_type').val();
        console.log("Client type changed to:", userType);

        if (userType === 'legal') {
            $('.legal-fields').show();
            $('.real-fields').hide();

            $('#company_name').prop('required', true);
            $('#company_national_id').prop('required', true);
            $('#company_phone').prop('required', false); // تلفن ثابت اختیاری
            $('#company_address').prop('required', true);

            $('#national_code').prop('required', false);
            $('#birth_date_jalali').prop('required', false);
        } else { // userType === 'real'
            $('.legal-fields').hide();
            $('.real-fields').show();

            $('#company_name').prop('required', false);
            $('#company_national_id').prop('required', false);
            $('#company_phone').prop('required', false);
            $('#company_address').prop('required', false);

            $('#national_code').prop('required', true);
            $('#birth_date_jalali').prop('required', true);
        }
    }

    $('#user_type').on('change', function() {
        toggleClientTypeFields();
    });

    toggleClientTypeFields();


    // *** فعال سازی Persian Datepicker برای فیلدهای جدید ***
    $('.persian-datepicker').persianDatepicker({
        format: 'YYYY/MM/DD',
        altField: '.alt-datepicker-gregorian',
        altFormat: 'YYYY-MM-DD',
        initialValueType: 'persian',
        autoClose: true
    });

    // *** فعال سازی Datepicker برای مودال ثبت لاگ ***
    $('#log_date_jalali').persianDatepicker({
        format: 'YYYY/MM/DD HH:mm:ss',
        altField: '#log_date',
        altFormat: 'YYYY-MM-DD HH:mm:ss',
        initialValueType: 'persian',
        autoClose: true,
        timePicker: {
            enabled: true,
            meridian: {
                enabled: true
            },
        },
    });

    // *** منطق برای مودال ثبت لاگ ***
    $('.add-log-btn').on('click', function() {
        var clientId = $(this).data('client-id');
        $('#log_client_id').val(clientId);
        var today = new persianDate();
        $('#log_date_jalali').val(today.format('YYYY/MM/DD'));
        $('#log_date').val(today.gDate.toISOString().slice(0, 19).replace('T', ' '));
    });
});