<?php
session_start();
include 'database.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['crop_image'])) {
 $curl = curl_init();

$cfile = new CURLFile($targetFile, mime_content_type($targetFile), basename($targetFile));

curl_setopt_array($curl, [
    CURLOPT_URL => "http://localhost:5000/predict",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => ['image' => $cfile],
    CURLOPT_HTTPHEADER => ["Content-Type: multipart/form-data"]
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "<p style='color:red;'>cURL Error: $err</p>";
} else {
    $result = json_decode($response, true);
    if (isset($result['prediction'])) {
        echo "<p style='color:green;'>Prediction: " . htmlspecialchars($result['prediction']) . "</p>";
    } else {
        echo "<p style='color:red;'>Error: Invalid response from Python API.</p>";
    }
}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Crop Doctor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f7;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            color: #2d6a4f;
        }

        input[type="file"] {
            margin: 20px 0;
            padding: 10px;
        }

        button {
            background-color: #2d6a4f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        button:hover {
            background-color: #1b4332;
        }

        .result {
            font-size: 18px;
            color: #333;
            margin-top: 20px;
            padding: 10px;
            background: #e9f5ec;
            border-left: 5px solid #2d6a4f;
        }

        img.preview {
            max-width: 100%;
            margin-top: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Smart Crop Doctor</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="image" id="imageInput" accept="image/*" required>
        <br>
        <button type="submit">Predict Disease</button>
    </form>

    <img id="previewImage" class="preview" style="display:none;"/>
    <div id="result" class="result" style="display:none;"></div>
</div>

<script>
    const form = document.getElementById('uploadForm');
    const resultDiv = document.getElementById('result');
    const previewImage = document.getElementById('previewImage');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const fileInput = document.getElementById('imageInput');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select an image file');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('image', file);

        fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
           resultDiv.innerText = "Prediction: " + JSON.stringify(data.prediction);

            resultDiv.style.display = 'block';
        })
        .catch(error => {
            resultDiv.innerText = "Error: " + error;
            resultDiv.style.display = 'block';
        });
    });
</script>d

</body>
</html>

