
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment Form</title>
    <style>
/* Container for the form */
/* Container for the form */
body, html {
    height: 100%;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

form {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    padding: 20px;
    max-width: 400px;
    width: 100%; /* Ensure the form takes full width */
}


/* Labels for the form fields */
label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

/* Radio button container */
.radio-container {
    display: inline-block;
    margin-bottom: 10px;
}

/* Radio button input */
.radio-container input[type="radio"] {
    display: none;
}

/* Radio button label */
.radio-container label {
    cursor: pointer;
    padding-left: 25px; /* Adjust spacing between label and radio button */
    position: relative;
}

/* Radio button visual indicator */
.radio-container label:before {
    content: "";
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid #ccc;
    border-radius: 50%;
    position: absolute;
    left: 0;
    top: 1px;
}

/* Radio button visual indicator (checked state) */
.radio-container input[type="radio"]:checked + label:before {
    background-color: #0866ff; /* Color when radio button is checked */
    border-color: #0866ff;
}

/* Input fields */
input[type="text"] {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 10px;
}

/* Submit button */
input[type="submit"] {
    background-color: #0866ff;
    color: #fff;
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    width: 100%;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1), 0px 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

/* Submit button hover effect */
input[type="submit"]:hover {
    background-color: #0056b3; /* Darker shade of blue on hover */
    box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.2), 0px 2px 4px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

img{
    width: 400px;
    height: 60px;
}

    </style>
</head>

<body>


    <?php
    session_start();
    $errors = [];

    $email = $_SESSION["email"];
    $con = mysqli_connect("localhost", "root", "", "medicare");
    $sql5 = "SELECT user_id FROM loginfo WHERE email ='$email'
    UNION
    SELECT user_id FROM superadmin WHERE email ='$email'  
    UNION
    SELECT user_id FROM admin WHERE email ='$email'";
    $result = mysqli_query($con, $sql5);
    $row1 = mysqli_fetch_assoc($result);
    $userId = $row1['user_id'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {



        // Validate card type
        $cardType = $_POST["card_type"] ?? '';
        if (!in_array($cardType, ['visa', 'mastercard'])) {
            $errors[] = "Invalid card type";
        }

        // Validate card number
        $cardNumber = $_POST["card_number"] ?? '';
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            $errors[] = "Invalid card number. It should be a 16-digit number.";
        }

        // Validate expiry date
        $expiryDate = $_POST["expiry_date"] ?? '';
        if (!preg_match('/^\d{2}\/\d{2}$/', $expiryDate)) {
            $errors[] = "Invalid expiry date. It should be in MM/YY format.";
        }

        // Validate CVV
        $cvv = $_POST["cvv"] ?? '';
        if (!preg_match('/^\d{3}$/', $cvv)) {
            $errors[] = "Invalid CVV. It should be a 3-digit number.";
        }

        // Validate cardholder name
        $cardholderName = $_POST["cardholder_name"] ?? '';
        if (empty($cardholderName)) {
            $errors[] = "Cardholder name is required.";
        }

        // If there are no errors, you can process the payment or perform other actions
        if (empty($errors)) {


            $sql13 = "SELECT product_id, qty FROM cart WHERE user_id='$userId'";
            $result13 = mysqli_query($con, $sql13);
            $num = mysqli_num_rows($result13);
            $idOut = array();
            $idOut2 = array();

            while ($row13 = mysqli_fetch_assoc($result13)) {
                $product_id = $row13['product_id'];
                $qty = $row13['qty'];

                $sql35 = "SELECT qty_p FROM products WHERE id ='$product_id'";
                $result35 = mysqli_query($con, $sql35);
                $row35 = mysqli_fetch_assoc($result35);

                $sql37 = "SELECT productName FROM products WHERE id ='$product_id'";
                $result37 = mysqli_query($con, $sql37);
                $row37 = mysqli_fetch_assoc($result37);
                $productName = $row37['productName'];
                $qty_p = $row35['qty_p'];

                if ($row35['qty_p'] == 0) {
                    $sql36 = "DELETE FROM cart WHERE user_id='$userId' AND product_id='$product_id'";
                    $result36 = mysqli_query($con, $sql36);
                    $idOut[] = $row37['productName'];
                } else if ($row35['qty_p'] < $qty) {
                    $sql50 = "UPDATE cart SET qty = '$qty_p' WHERE user_id='$userId' AND product_id='$product_id'";
                    $result50 = mysqli_query($con, $sql50);
                    $idOut2[] = $row37['productName'];
                }
            }


            $count = count($idOut);
            $count2 = count($idOut2);



            if (!empty($idOut) && empty($idOut2)) {

                if ($count == $num) {

                    if ($count == 1) {
                        echo "<script>alert('The following product is out of stock: " . implode(", ", $idOut) . "'); window.location.href = 'index.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('The following products are out of stock: " . implode(", ", $idOut) . "'); window.location.href = 'index.php';</script>";
                        exit;
                    }
                } else {
                    if ($count == 1) {
                        echo "<script>alert('The following product is out of stock: " . implode(", ", $idOut) . "'); window.location.href = 'check-out.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('The following products are out of stock: " . implode(", ", $idOut) . "'); window.location.href = 'check-out.php';</script>";
                        exit;
                    }
                }
            }

            if (!empty($idOut) && !empty($idOut2)) {


                if ($count == 1) {
                    echo "<script>alert('The following product is out of stock: " . implode(", ", $idOut) . "'); </script>";
                } else {
                    echo "<script>alert('The following products are out of stock: " . implode(", ", $idOut) . "');</script>";
                }
            }





            if ($count2 == 1) {
                echo "<script>alert('The following product cannot satisfy the requested amount, and we will update you on how much of this product you can buy: " . implode(", ", $idOut2) . "'); window.location.href = 'check-out.php';</script>";
                exit;
            }

            if ($count2 > 1) {

                echo "<script>alert('The following products cannot satisfy the requested amount, and we will update you on how much of these products you can buy: " . implode(", ", $idOut2) . "'); window.location.href = 'check-out.php';</script>";
                exit;
            }


            // Fetching user ID from session
            $email = $_SESSION["email"];
            $con = mysqli_connect("localhost", "root", "", "medicare");
            $sql5 = "SELECT user_id FROM loginfo WHERE email ='$email'
            UNION
            SELECT user_id FROM superadmin WHERE email ='$email'  
            UNION
            SELECT user_id FROM admin WHERE email ='$email'";
            $result = mysqli_query($con, $sql5);
            $row1 = mysqli_fetch_assoc($result);
            $userId = $row1['user_id'];


            // Loop through each product in the cart and insert into the 'pay' table
            $sql13 = "SELECT product_id, qty FROM cart WHERE user_id='$userId'";
            $result13 = mysqli_query($con, $sql13);

            while ($row13 = mysqli_fetch_assoc($result13)) {
                $product_id = $row13['product_id'];
                $qty = $row13['qty'];


                $sql36 = "UPDATE products SET qty_p = qty_p - '$qty' WHERE id = '$product_id'";
                $result36 = mysqli_query($con, $sql36);

                // Insert the product into the 'pay' table
                $sql14 = "INSERT INTO pay (user_id, product_id, qty) VALUES ('$userId', '$product_id', '$qty')";
                $result14 = mysqli_query($con, $sql14);



                $con = mysqli_connect("localhost", "root", "", "medicare");

                $email = $_SESSION["email"];
                $sql5 = "SELECT user_id FROM loginfo WHERE email ='$email'
                        UNION
                        SELECT user_id FROM superadmin WHERE email ='$email'  
                        UNION
                        SELECT user_id FROM admin WHERE email ='$email'";
                
                $result = mysqli_query($con, $sql5);
                $row1 = mysqli_fetch_assoc($result);
                
                $sql = "SELECT ord_date_time, user_id FROM pay ORDER BY ord_date_time ASC";
                $result = mysqli_query($con, $sql);
                $dateArray = array();
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $dateArray[] = $row['ord_date_time'];}
                $order_number = 1;
                $unique_array = array_unique($dateArray);
                
                foreach ($unique_array as $date) {
                
                    $sql = "SELECT * FROM pay WHERE  ord_date_time='$date'";
                    $result = mysqli_query($con, $sql);
                    $row_count = mysqli_num_rows($result);
                   
                    $i=0;
                    $j=0;
                    while ($row = mysqli_fetch_assoc($result)) {
                
                       $ord_date_time_new = $row['ord_date_time'];
                        $product_id = $row['product_id'];
                        $sql1 = "SELECT * FROM products WHERE id='$product_id'";
                        $result1 = mysqli_query($con, $sql1);
                        $row1 = mysqli_fetch_assoc($result1);
                        $product_name = $row1['productName'];
                        $product_price = $row1['price'];
                        $product_image = $row1['fileDestination'];
                        $filename = "uploads/" . basename($product_image);
                        $product_quantity = $row['qty'];
                        $user_id = $row['user_id'];
                        $status = "proccesing";
                        $sql100 ="INSERT INTO statusTable (order_id, user_id, product_id, status) VALUES ('$order_number', '$user_id', '$product_id', ' $status')";
                        $result100 = mysqli_query($con, $sql100);
                        $sql101 = "SELECT status FROM statusTable WHERE order_id = '$order_number'";
                        $result101 = mysqli_query($con, $sql101);
                        $row101 = mysqli_fetch_assoc($result101);
                        $status1 = $row101['status'];
                
                 $i++;
                    }
                    $order_number++; 
                }



                if (!$result14) {
                    // Handle insertion error
                    echo "Error inserting product ID: $product_id into pay table.<br>";
                }
            }

            // Delete all products from the cart
            $sql15 = "DELETE FROM cart WHERE user_id='$userId'";
            $result15 = mysqli_query($con, $sql15);
            if (!$result15) {
                // Handle deletion error
                echo "Error deleting products from cart.<br>";
            }

            echo "<script>alert('Products have been successfully ordered'); window.location.href = 'index.php';</script>";
        }
    }
    ?>

    <form method="post" action="">
        <?php
        if (isset($_POST['submit'])) {
            // Display errors, if any
            if (!empty($errors)) {
                echo '<div class="error">' . implode('<br>', $errors) . '</div>';
            }
        }
        ?>


        <label>Card Type:</label>
        <span><input class="visa" type="radio" name="card_type" value="visa" required> Visa</span>
        <span><input class="master" type="radio" name="card_type" value="mastercard" required> MasterCard</span>


        <label for="card_number">Card Number:</label>
        <input type="text" name="card_number" id="card_number" required>

        <label for="expiry_date">Expiry Date (MM/YY):</label>
        <input type="text" name="expiry_date" id="expiry_date" pattern="\d{2}/\d{2}" placeholder="MM/YY" required>

        <label for="cvv">CVV:</label>
        <input type="text" name="cvv" id="cvv" pattern="\d{3}" placeholder="123" required>

        <label for="cardholder_name">Cardholder Name:</label>
        <input type="text" name="cardholder_name" id="cardholder_name" required>

        <div class="card-type">
            <img src="./uploads/pngwing.com.png" alt="Visa">

        </div>

        <input type="submit" name="submit" value="Submit">


    </form>

</body>

</html>