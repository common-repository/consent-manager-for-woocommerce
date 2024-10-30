(function($) {
    $(document).ready(function () {

        var date = new Date();

        $('#post-date-picker').datepicker({
            dateFormat: 'dd.mm.yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "2000:" + date.getFullYear()
        });

        $('#sort_by_number').on('click', function() {
            var sortValue = $(this).data('value'),
                sortOrder = sortValue == 'asc' ? 'desc' : 'asc',
                currentUrl = window.location.href;

            window.location.href = window.location.href + '&sort=' + sortOrder;
        });

        $('#to_csv').on('click', function() {
            $('.ajax_loader').css('display', 'block');
            $('.ajax_success').hide();
            $('.ajax_error').hide();
            $.ajax({
                url: CMW_variables.admin_url + 'admin-ajax.php?action=getAdultsReport',
                beforeSend: function() {
                    $('.ajax_loader').css('display', 'block');
                    $('.ajax_success').hide();
                    $('.ajax_error').hide();
                },
                success: function() {
                    window.location.href = CMW_variables.content_url + '/uploads/search_and_reports.csv';
                    $('.ajax_loader').hide();
                    $('.ajax_success').show();
                    $('.ajax_error').hide();
                },
                error: function() {
                    $('.ajax_loader').hide();
                    $('.ajax_success').hide();
                    $('.ajax_error').show();
                }
            });
        });

        $('#to_csv_pro').on('click', function() {
            $('.ajax_loader').css('display', 'block');
            $('.ajax_success').hide();
            $('.ajax_error').hide();
            $.ajax({
                url: CMW_variables.admin_url + 'admin-ajax.php?action=getReportsList',
                beforeSend: function() {
                    $('.ajax_loader').css('display', 'block');
                    $('.ajax_success').hide();
                    $('.ajax_error').hide();
                },
                success: function() {
                    window.location.href = CMW_variables.content_url + '/uploads/search_and_reports.csv';
                    $('.ajax_loader').hide();
                    $('.ajax_success').show();
                    $('.ajax_error').hide();
                },
                error: function() {
                    $('.ajax_loader').hide();
                    $('.ajax_success').hide();
                    $('.ajax_error').show();
                }
            });
        });

        $('.child_photo').on('click', function() {
            var child_id = $(this).data('child');
            var parent_id = $(this).data('parent');
            var src;

            if (parent_id == child_id) { // adult photo
                src = CMW_variables.content_url + '/uploads/adult_photo/' + child_id + '.jpg';
            } else { // child photo
                src = CMW_variables.content_url + '/uploads/children_photo/' + parent_id + '/' + child_id + '.jpg';
            }

            $('#child_photo_modal img').attr('src', src);
            $('#child_photo_modal').modal('show');
        });

        $(".cm_hint").tipTip({maxWidth: "200px", edgeOffset: 10});



    });
})(jQuery);
