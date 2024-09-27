<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">

    <h1>User Details</h1>
    <div id="errorMessages" style="color: red;"></div>

    <form id="userForm" enctype="multipart/form-data" class="row g-3" novalidate>
        @csrf
        <div class="form-group col-md-6">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group col-md-6">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group col-md-6">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" name="phone" placeholder="Phone" pattern="^[789]\d{9}$" maxlength="10" title="Please enter a valid Indian mobile number (10 digits starting with 7, 8, or 9)" required>

        </div>
        
        <div class="form-group col-md-6">
            <label for="role_id">Role:</label>

            <select name="role_id" id="role_id" class="form-control" required>
                <option selected  value="">Choose role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-12">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group col-md-12">
            <label for="profile_image">Profile Image:</label>
            <input type="file" class="form-control-file" id="profile_image" name="profile_image" accept="image/*">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <h2 class="mt-5">Submitted User Details</h2>


    <table id="userTable" class="table" border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Role</th>
                <th>Profile Image</th>
            </tr>
        </thead>
        <tbody>
            <!-- Users will be dynamically inserted here -->
        </tbody>
    </table>
</div>

  <!--Just Bootstrap JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {

            fetchUsers();

            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous error messages
                $('#errorMessages').html('');

                // Basic client-side validation
                let isValid = true;
                // console.log('name',isValid)

                // Validate name
                if (!$('input[name="name"]').val()) {
                    // console.log('name')
                    isValid = false;
                    $('#errorMessages').append('<li>Name is required.</li>');
                }

                // Validate email
                const email = $('input[name="email"]').val();
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    // console.log('email')

                    isValid = false;
                    $('#errorMessages').append('<li>Please enter a valid email address.</li>');
                }

                // Validate phone
                const phone = $('input[name="phone"]').val();
                const phonePattern = /^[789]\d{9}$/;
                if (!phone || (phone && !/^\d+$/.test(phone)) || (!phonePattern.test(phone))) {
                    // console.log('phone')
                    isValid = false;
                    $('#errorMessages').append('<li>Please enter a valid Indian mobile number (10 digits, starting with 7, 8, or 9).</li>');
                }

                // Validate role ID
                const roleId = $('select[name="role_id"]').val();
                if (!roleId || roleId < 1) {
                    // console.log('roleId',roleId)

                    isValid = false;
                    $('#errorMessages').append('<li>Role ID is required and must be a positive integer.</li>');
                }
                
                // Proceed with AJAX call only if the form is valid
                if (isValid) {

                    let formData = new FormData(this);
                    
                    $.ajax({
                        url: "/api/users",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // console.log(response)
                            if (response.success) {
                                fetchUsers();
                                $('#userForm')[0].reset();
                                $('#errorMessages').html(''); // Clear previous errors
                                $('#userForm').removeClass('was-validated');

                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                // Display validation errors
                                let errors = xhr.responseJSON.errors;
                                let errorHtml = '<ul>';
                                for (let key in errors) {
                                    errorHtml += `<li>${errors[key].join(', ')}</li>`;
                                }
                                errorHtml += '</ul>';
                                $('#errorMessages').html(errorHtml);
                            }
                        }
                    });
                }
            });

            function fetchUsers() {
                // console.log('coming')
                $.ajax({
                    url: "/api/users",
                    method: 'GET',
                    success: function(users) {
                        let userTableBody = '';
                        users.forEach(function(user) { //console.log(user)
                            userTableBody += `<tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.phone}</td>
                                <td>${user.description}</td>
                                <td>${user.role.name}</td>
                                <td><img src="/storage/${user.profile_image}" width="50" height="50"></td>
                            </tr>`;
                        });
                        $('#userTable tbody').html(userTableBody);
                    }
                });
            }
        });


    //When the DOM is loaded, apply this
    window.addEventListener('DOMContentLoaded', () => {
        //Get the form ID
        const form = document.getElementById("userForm");
        //on submit form, check if valid
        form.addEventListener("submit", (e)=>{
            if (form.checkValidity() === false) {
            e.preventDefault();
            e.stopPropagation();
            }
            form.classList.add("was-validated");
        })
    });
    </script>
</body>
</html>
