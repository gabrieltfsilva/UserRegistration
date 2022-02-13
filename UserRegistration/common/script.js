
/*
 * This validation avoids an unnecessary request.
 * It must also be done on the server side.
 * Validating only on the client side is a vulnerability.
 */
function validateUser() {
    var name = document.getElementById('name').value;
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    if (name.trim().length == 0 || name.trim().length > 20) {
        alert('Your name ais required and must be between 8-20 characters.');

        return false
    }

    if ((username.trim().length >= 8) &&
        (password.trim().length >= 8) &&
        (username.trim().length <= 20) &&
        (password.trim().length <=20)) {

        return true;
    } else {
        alert('Your username and password must be between 8-20 characters.');
        return false;
    }
}

function validateLogin() {
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    if ((username.trim().length >= 8) &&
        (password.trim().length >= 8) &&
        (username.trim().length <= 20) &&
        (password.trim().length <=20)) {

        return true;
    } else {
        alert('Your username and password must be between 8-20 characters.');
        return false;
    }
}