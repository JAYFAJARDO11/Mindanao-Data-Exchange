<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found</title>
    <style>
        /* Common background styles for MDX */
        body {
            background: linear-gradient(135deg, #e0e5ec 0%, #c4c6ca 100%);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding: 50px;
        }

        /* Block pattern overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(to right, rgba(180, 190, 200, 0.3) 1.5px, transparent 1.5px),
                linear-gradient(to bottom, rgba(180, 190, 200, 0.3) 1.5px, transparent 1.5px);
            background-size: 25px 25px;
            z-index: -1;
            pointer-events: none;
        }

        img {
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
            background-color: #cc0000;       /* Matches your h1 red */
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
            background-color: #990000;      /* Darker red on hover */
        }

        .btn:active {
            transform: translateY(2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <img src="/Mindanao-Data-Exchange/images/111.png" alt="The Rock suspicious face">
    <h1>404 - Page Not Found</h1>
    <p>The Rock took one look at your request and said: <br>“You sure this page ever existed?” 😏</p>
    <p>Either you typed the wrong URL, or the page got body-slammed out of existence.</p>
    <button onclick="window.location.href='/Mindanao-Data-Exchange/homelogin.php';" class="btn">Back to Home</button>
</body>
</html>
