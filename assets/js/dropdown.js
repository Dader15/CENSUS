$(document).ready(function () {
    $('.dropdown-item').click(function () {
        var selectedValue = $(this).data('value');
        $('#dropdowLane').html('<i class="fa fa-paper-plane"></i> ' + selectedValue);
        
    });
});