document.addEventListener("DOMContentLoaded", function () {
    function updateTable(data) {
        var tableHTML = "<table><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";

        data.forEach(function (row) {
            tableHTML += "<tr><td>" + row.fullName + "</td><td>" + row.email + "</td><td>" + row.role + "</td><td>" + row.created_at + "</td></tr>";
        });

        tableHTML += "</table>";

        document.getElementById('your_table_container_id').innerHTML = tableHTML;
    }

    setInterval(loadData, 5000); 
});


