document.querySelector('form').addEventListener('submit', function (e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const errorMessage = document.getElementById('error-message');

    if (username === "" || password === "") {
        e.preventDefault();
        errorMessage.innerText = "É necessário preencher todos os campos!";
    } else {
        errorMessage.innerText = ""; // Clear error message if form is valid
    }
});
