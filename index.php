<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrsEdu - Student Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .menu-btn {
            display: block;
            width: 100%;
            padding: 20px;
            margin: 15px 0;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .menu-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .menu-btn:active {
            transform: translateY(0);
        }
        .footer {
            margin-top: 40px;
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CrsEdu</h1>
        <p class="subtitle">Student Management System</p>
        
        <a href="students_list.php" class="menu-btn">📚 View Student List</a>
        <a href="daily_report.php" class="menu-btn">📝 Add Daily Report</a>
        
        <div class="footer">
            <p>&copy; 2025 CrsEdu. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
