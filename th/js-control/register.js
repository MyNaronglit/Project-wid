document.querySelector('form').addEventListener('submit', function(event) {
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirmPassword"]').value;
    if (password !== confirmPassword) {
        alert('Passwords do not match.');
        event.preventDefault();
    }
});