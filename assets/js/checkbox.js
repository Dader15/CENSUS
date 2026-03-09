function selectAll() {
        var checkboxes = document.querySelectorAll('.option');
        var selectAllCheckbox = document.getElementById('select-all');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    function updateSelectAll() {
        var checkboxes = document.querySelectorAll('.option');
        var selectAllCheckbox = document.getElementById('select-all');
        
        // Check if all checkboxes are checked to update the "Select All" checkbox
        var allChecked = true;
        checkboxes.forEach(function(checkbox) {
            if (!checkbox.checked) {
                allChecked = false;
            }
        });

        selectAllCheckbox.checked = allChecked;
}
    
