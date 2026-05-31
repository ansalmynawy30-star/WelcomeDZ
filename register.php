<?php
// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";
$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm-password'] ?? '';
    $role     = $_POST['role'] ?? 'customer';

    // Validation
    if (empty($name)) {
        $error_messages[] = "Full name is required";
    }
    if (empty($email)) {
        $error_messages[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages[] = "Invalid email format";
    }
    if (empty($password)) {
        $error_messages[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $error_messages[] = "Password must be at least 8 characters";
    }
    if ($password !== $confirm) {
        $error_messages[] = "Passwords do not match";
    }

    if (empty($error_messages)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users_new (name, email, phone, password, role) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_message = "Account created successfully!";
            $_POST = []; // Clear form
        } else {
            if ($conn->errno == 1062) {
                $error_messages[] = "Email already exists";
            } else {
                $error_messages[] = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <a href="index.html" class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-indigo-600">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </a>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account? 
                <a href="log in .html" class="font-medium text-indigo-600 hover:text-indigo-500">Log in</a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

                <?php if ($success_message): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                        <p class="text-sm text-green-700"><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_messages)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <ul class="list-disc list-inside text-sm text-red-700">
                            <?php foreach ($error_messages as $msg): ?>
                                <li><?php echo $msg; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input mt-1" required placeholder="Enter your full name"
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input mt-1" required placeholder="Enter your email address"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="mb-5">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" oninput="this.value = this.value.replace(/[^0-9+()\-\s]/g, '');"
                            onpaste="setTimeout(() => { this.value = this.value.replace(/[^0-9+()\-\s]/g, ''); }, 0);" id="phone" name="phone" class="form-input mt-1" placeholder="Enter your phone number"
                        
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                          
                   <div class="mb-5">
    <!-- <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
    <input type="tel"
           id="phone"
           name="phone"
           class="form-input mt-1"
           placeholder="Enter your phone number"
           autocomplete="off"
           inputmode="tel"
           pattern="[0-9+ -]*" 
           onkeydown="if (event.key === '(' || event.key === ')') {
                         event.preventDefault();
                      }"
           oninput="this.value = this.value.replace(/[()]/g, '').replace(/[^0-9+ -]/g, '');"
           onpaste="setTimeout(() => {
                       this.value = this.value.replace(/[()]/g, '').replace(/[^0-9+ -]/g, '');
                    }, 0);"
           value="  
           <?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
</div> -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" class="form-input mt-1" required placeholder="Enter your password">
                        <p class="text-xs text-gray-500 mt-2">Password must be at least 8 characters and include uppercase, lowercase, number, and special character</p>
                    </div>
                    <div class="mb-6">
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="form-input mt-1" required placeholder="Re-enter your password">
                    </div>

                    <div>
                        <button type="submit" class="form-button w-full" style="margin-top: 1rem;">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (!phoneInput) return;

    function forceClean() {
        phoneInput.value = phoneInput.value
            .replace(/[()]/g, '')              // يمسح القوسين أول شيء
            .replace(/[^0-9+ -]/g, '');       // يبقي أرقام + + - مسافة فقط
    }

    // تنظيف فوري
    forceClean();

    // تنظيف بعد 100ms (Chrome أحيانًا يضيف متأخر)
    setTimeout(forceClean, 100);
    setTimeout(forceClean, 300);

    // لو رجع للصفحة من الـ back/forward cache
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            forceClean();
        }
    });
});
</script>
</body>
</html>