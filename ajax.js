document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (event) {
        event.preventDefault();


        const formData = new FormData(form);

      I
        fetch(form.action, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
          
            document.querySelector('.layout').innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});