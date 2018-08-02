$('#signIn').on('submit', (e) => {
    e.preventDefault();
    let _username = $('#signIn #username').val();
    let _password = $('#signIn #password').val();
    $.ajax({
        url: '/login',
        type: 'POST',
        data: {_username, _password},
        success: response => {
            document.location.href = '/';
        },
        error: err => {
            console.log(err);
            $('.alert-error').show();
            $('.alert-error').empty();
            $('.alert-error').text(err.responseJSON.err);
        }
    });
});