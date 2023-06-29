<?php
    function isValidURL($url) {
        // Check if the URL is not empty or contains only whitespaces
        if (trim($url) === '') {
            return false;
        }

        // Regular expression pattern for URL validation
        $validation = '/^(https?:\/\/)?([a-z0-9-]+\.?)+(\/[a-z0-9\-._~:\/\?#\[\]@!$&\'()*+,;=%]*)?$/i';

        // Check if the URL matches the pattern
        if (!preg_match($validation, $url)) {
            return false;
        }

        // URL is valid
        return true;
    }

    function sanitizeLink($input) {
        // Convert special characters to HTML entities to prevent XSS
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    function sanitizeText($input) {
        $input = trim($input);
        return htmlspecialchars($input);
    }

    function sanitizeDate($input) {
        $sanDate = date('Y-m-d', strtotime($input));

        $currentDate = date('Y-m-d');

        if($sanDate > $currentDate) {
            $errorDate = "";
            return $errorDate;
        }

        return $sanDate;
    }

    function getUserRole($conn, $username) {
        $query = "SELECT Role_Name FROM roles r JOIN users u ON r.Role_Id = u.Role WHERE Username = '$username'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            return $row['Role_Name'];
        }

        // Return a default role if the user is not found or there's an error
        return "guest";
    }
?>