function updateYearOptions() {
    var courseSelect = document.getElementById("course");
    var yearSelect = document.getElementById("year");

    // Clear previous options
    yearSelect.innerHTML = '<option value="" disabled selected>Select Year</option>';

    // Add options based on the selected course
    if (courseSelect.value === "CSE" || "EE" || "CE") {
        addYearOption(yearSelect, "First");
        addYearOption(yearSelect, "Second");
        addYearOption(yearSelect, "Third");
       
    } 
}

function addYearOption(selectElement, year) {
    var option = document.createElement("option");
    option.value = year;
    option.text = year;
    selectElement.add(option);
}

// aadhaar validation
function validateAadhaarInput() {
    var aadhaarInput = document.getElementById('aadhaar');
    var validationMsg = document.getElementById('aadhaarValidationMsg');

    if (aadhaarInput.value.length < 12) {
        // Display a message if Aadhaar number is too short
        validationMsg.innerText = "Aadhaar number is too short.";
    } else if (aadhaarInput.value.length > 12) {
        // Display a message if Aadhaar number is too long
        validationMsg.innerText = "Aadhaar number is too long. Please enter only 12 digits.";
    } else {
        // Clear the validation message if Aadhaar number is valid
        validationMsg.innerText = "";
    }
}

//phone no validation

function validatePhoneInput() {
    var phoneInput = document.getElementById('phone');
    var validationMsg = document.getElementById('phoneValidationMsg');

    // Remove non-digit characters from the input
    var phoneNumber = phoneInput.value.replace(/\D/g, '');

    if (phoneNumber.length !== 10) {
        // Display a message if the phone number is not 10 digits
        validationMsg.innerText = "Phone number must be 10 digits.";
    } else {
        // Clear the validation message if the phone number is valid
        validationMsg.innerText = "";
    }
}
//image previewer
function previewImage(input) {
    const fileInput = input;
    const previewImage = input.id === 'passportPhoto' ? document.getElementById('passportPhotoPreview') : document.getElementById('signImagePreview');

    const file = fileInput.files[0];

    if (file) {
        // Check file type
        const fileType = file.type;
        if (fileType !== 'image/png' && fileType !== 'image/jpeg') {
            alert('Please upload a PNG or JPEG image.');
            fileInput.value = ''; // Clear the input field
            return;
        }

        // Check file size
        const fileSize = file.size;
        const maxSizeInBytes = 500 * 1024; // 500KB
        if (fileSize > maxSizeInBytes) {
            alert('File size exceeds the limit of 500KB.');
            fileInput.value = ''; // Clear the input field
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
        };

        reader.readAsDataURL(file);
    }
}
// when userfill aadhaar no

function fetchUserDetails() {
    var aadhaar = document.getElementById('aadhaar').value;

    // Make an AJAX request to fetch user details
    fetch('http://localhost/poly/app.php?aadhaar=' + aadhaar)
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                // Populate form fields with fetched details
                document.getElementById('name').value = data.name;
                document.getElementById('fatherName').value = data.fatherName;
                document.getElementById('institutename').value = data.institutename;
            } else {
                // Handle error (user not found)
                alert('User not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


// when userclick on submit

function handleSubmit() {

    var tokenInput = prompt("Please enter the token number:");

    if (tokenInput !== null) {
        // If the user provides a token number, add it to the form data
        document.getElementById("registrationForm").insertAdjacentHTML(
            'beforeend',
            `<input type="hidden" name="token" value="${tokenInput}">`
        );

        // Submit the form with the updated data
        document.getElementById("registrationForm").submit();
    } else {
        // If the user cancels the prompt, show a message or handle it accordingly
        alert("Form submission canceled. Token number not provided.");
    }
}


