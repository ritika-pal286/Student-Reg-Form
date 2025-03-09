<?php

require 'dompdf/dompdf/autoload.inc.php';
use Dompdf\Options;
use Dompdf\Dompdf;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Content-Type: text/html');

// MySQL configurations
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',  
    'database' => 'poly_info',
    'cursorclass' => PDO::FETCH_ASSOC,
];

try {
    // Database connection
    $connection = new PDO(
        'mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['database'],
        $db_config['user'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Function to upload image

function uploadImage($file, $uploadDir) {
    $targetDir = $uploadDir . '/';
    $targetFile = $targetDir . basename($file['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file['tmp_name']);
    if($check === false) {
        return false; // Invalid image file
    }

    // Allow certain file formats
    if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
        return false; // Unsupported file format
    }

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $targetFile; // Return relative path to the uploaded image
    } else {
        return false; // Failed to upload image
    }
}
// PDF generation function
function generatePDF($data) {
    // Initialize Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);

    // Load HTML content
    $html = '
   <html>
   <head>
   <title>PDF form </title>
   </head>

   <body style="font-family: Arial, sans-serif;  margin: 20px; text-align: center; ">
       
       <h2>DEV BHOOMI INSTITUTION OF POLYTECHNIC, SAHARANPUR</h2>
       <h1>Pre-Request Form</h1>
       <hr>
       <table  style="width: 100%; border-collapse: collapse; margin: 20px; text-align: left;">
           ';

    // Loop through form data and add to HTML
    foreach ($data as $key => $value) {
        if ($key === 'Photo' || $key === 'signImage') {
            // If the key is an image, embed the image as base64-encoded string
            $imageData = file_get_contents($value);
            $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
            $html .= '
           <tr>
               <th style="border: 2px solid #333;padding: 10px; ">' . ucfirst($key) . '</th>
               <td style="border: 2px solid #333;padding: 10px;"><img src="' . $base64Image . '" style="max-width: 200px; max-height: 200px;"></td>
           </tr>
           ';
        } else {
            $html .= '
           <tr>
               <th style="border: 2px solid #333; padding: 10px; ">' . ucfirst($key) . '</th>
               <td style="border: 2px solid #333; padding: 10px;">' . $value . '</td>
           </tr>
           ';
        }
    }

    $html .= '</table>
   
   </body>
   </html>';

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);

    // Set paper size
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF (first output on a variable)
    $dompdf->render();

    return $dompdf->output();
}

function fetchUserDetails($aadhaar, $connection) {
    $sql = "SELECT name, fatherName, institutename FROM poly WHERE aadhaar = :aadhaar";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':aadhaar', $aadhaar, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch();

    return $result;
}

// Function to check if token is valid
function isValidToken($token, $connection) {
    $sql = "SELECT * FROM poly WHERE token = :token";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch();

    return $result !== false;
}

// Function to store form data in the database
function storeFormData($data, $connection) {
    $token = $data['token'];

    if (isValidToken($token, $connection)) {
        $sqlUpdateData = "UPDATE poly SET name = :name, fatherName = :fatherName, email = :email, aadhaar = :aadhaar, phone = :phone, rollno = :rollno, institutename = :institutename,  dob = :dob, branch = :branch, year = :year, Photo = :Photo, signImage = :signImage WHERE token = :token";

        $stmt = $connection->prepare($sqlUpdateData);
        $stmt->execute($data);
    } else {
        $sqlInsertData = "INSERT INTO poly (name, fatherName, aadhaar, phone, rollno, institutename, dob, branch, year, Photo, signImage,  token) VALUES (:name, :fatherName, :aadhaar, :phone, :rollno, :institutename, :dob, :branch, :year, :Photo,  :token)";

        $stmt = $connection->prepare($sqlInsertData);
        $stmt->execute($data);
    }
}

// Handling GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $aadhaar = filter_input(INPUT_GET, 'aadhaar', FILTER_SANITIZE_STRING);

    if ($aadhaar !== null) {
        $userDetails = fetchUserDetails($aadhaar, $connection);

        if ($userDetails) {
            echo json_encode($userDetails);
        } else {
            echo json_encode(['error' => 'User not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid or missing Aadhaar parameter']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'Photo' => isset($_FILES['Photo']) ? uploadImage($_FILES['Photo'], 'data/photo') : null,
        'signImage' => isset($_FILES['signImage']) ? uploadImage($_FILES['signImage'], 'data/sign') : null,
        'name' => isset($_POST['name']) ? $_POST['name'] : null,
        'fatherName' => isset($_POST['fatherName']) ? $_POST['fatherName'] : null,
        'email' => isset($_POST['email']) ? $_POST['email'] : null,
        'aadhaar' => isset($_POST['aadhaar']) ? $_POST['aadhaar'] : null,
        'phone' => isset($_POST['phone']) ? $_POST['phone'] : null,
        'rollno' => isset($_POST['rollno']) ? $_POST['rollno'] : null,
        'institutename' => isset($_POST['institutename']) ? $_POST['institutename'] : null,
        'dob' => isset($_POST['dob']) ? $_POST['dob'] : null,
        'branch' => isset($_POST['branch']) ? $_POST['branch'] : null,
        'year' => isset($_POST['year']) ? $_POST['year'] : null,
        'token' => isset($_POST['token']) ? $_POST['token'] : null,
    ];

    // Check if required parameters are set
    if (in_array(null, $data, true)) {
        echo json_encode(['error' => 'Invalid or missing parameters']);
        exit;
    }

    $token = $data['token'];

    if (isValidToken($token, $connection)) {
        storeFormData($data, $connection);

        $pdfData = generatePDF($data); // Pass user data to generatePDF

        echo '<html><head><title>Form Submitted</title></head><body style="font-family: Arial, sans-serif; margin: 20px; text-align: center;">';
echo '<script>
    alert("Form submitted successfully!");

    document.write(\'<h4>DEV BHOOMI INSTITUTION OF POLYTECHNIC, SAHARANPUR</h4>\');
    document.write(\'<h2>Pre-Request Form</h2>\');
    document.write(\'<table style="width: 40%; border-collapse: collapse; margin: 20px auto; text-align: left;">\');

    // Loop through form data and add to HTML
    var formData = ' . json_encode($data) . ';
    for (var key in formData) {
        document.write(\'<tr>\');
        document.write(\'<th style="border: 2px solid #333; padding: 10px;background-color: #f0f8ff;">\' + key.charAt(0).toUpperCase() + key.slice(1) + \'</th>\');
        
        if (key === "Photo" || key === "signImage") {
            document.write(\'<td style="border: 2px solid #333; padding: 10px;background-color: #f0f8ff;"><img src="\' + formData[key] + \'" style="max-width: 200px; max-height: 200px;"></td>\');
        } else {
            document.write(\'<td style="border: 2px solid #333; padding: 10px;background-color: #f0f8ff;">\' + formData[key] + \'</td>\');
        }
        
        document.write(\'</tr>\');
    }

    document.write(\'</table>\');
    document.write(\'<button id="printPdfButton" onclick="printPdf()" style="padding: 10px; border: 1px solid black; cursor:pointer;  border-radius: 10px;">Print PDF</button>\');

    function printPdf() {
        var pdfWindow = window.open("");
        pdfWindow.document.write("<html><head><title>Print</title></head><body><iframe width=\'100%\' height=\'100%\' src=\'data:application/pdf;base64,' . base64_encode($pdfData) . '\'></iframe></body></html>");
        pdfWindow.document.close();
    }
</script>';
echo '</body></html>';

exit;

    } else {
        // Token is not valid, redirect to index.html
        echo '<script>
        alert("Token not found! Form submission canceled.");
        window.location.href = "http://localhost/poly/index.html";
        </script>';
    }
} else {
    echo json_encode(['error' => 'Database connection not available']);
}

?>
