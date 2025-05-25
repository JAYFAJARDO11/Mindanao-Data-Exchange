<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dataset Not Found</title>
    <?php include __DIR__ . '/includes/background_styles.php'; ?>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .gif-image {
            width: 400px;
            max-width: 90%;
            margin-bottom: 20px;
            margin-top: 30px;
        }
        h1 {
            font-size: 48px;
            color: #cc0000;
        }
        p {
            font-size: 30px;
            color: #333;
            margin: 10px auto;
            max-width: 500px;
        }
        .btn {
            background-color: #cc0000;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 40px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
        .btn:hover {
            background-color: #990000;
        }
        .btn:active {
            transform: translateY(2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <img src="images/whathuh.gif" alt="What Huh GIF" class="gif-image" />
    <h1>Dataset Not Found!</h1>
    <p>Hmm... this dataset is playing hide and seek. 🤔</p>
    <p>Either it never existed, or it’s gone incognito.</p>
    <p>Try checking the URL or pick a different dataset!</p>
    <button onclick="window.location.href='/Mindanao-Data-Exchange/datasets.php';" class="btn">Back to Datasets</button>
</body>
</html>