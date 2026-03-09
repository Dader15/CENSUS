// LOGIN AJAX
$(document).ready(function() {
    $('#submitLogin').click(function(e) {
        e.preventDefault();
        var usr = $('#usr').val();
        var pss = $('#pss').val();
        $.ajax({
            type: 'POST',
            url: 'assets/php/login.php',
            data: {
                usr: usr,
                pss: pss
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        swal({
                            title: 'Login Successful!',
                            text: data.message,
                            icon: 'success',
                            button: false,
                        });
                        setTimeout(function(){
                            swal.close();
                            window.location.href = data.redirect_url;
                        }, 2000);
                    } else {
                        swal({
                            title: 'Login Unsuccessful!',
                            text: data.message,
                            icon: 'error',
                            button: false,
                        });
                        setTimeout(function(){
                            swal.close();
                        }, 2000);
                    }
                } catch (error) {
                    console.error('Invalid JSON response: ' + response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status, error);
            }
        });
    });
});

// LOGOUT AJAX
$(document).ready(function() {
    $("#submitLogout").click(function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will be logged out!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "../assets/php/logout.php",
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Logged Out!',
                                text: 'You have been successfully logged out.',
                                icon: 'success',
                                timer: 1000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '../index.php';
                            });
                        } else {
                            console.log(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
});