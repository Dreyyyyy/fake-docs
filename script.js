document.querySelector('form').addEventListener('submit', function (e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username === "" || password === "") {
        e.preventDefault();

        document.getElementById('error-message').innerText = "É necessário preencher todos os campos!";
    }
});