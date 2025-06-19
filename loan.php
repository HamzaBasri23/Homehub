<?php
session_start();
include("config.php");

// Session check to ensure the user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Loan Application Form</title>
</head>

<body>
    <div id="page-wrapper">
        <div class="row">
            <?php include("include/header.php"); ?>

            <div class="page-wrappers login-body full-row bg-gray">
                <div class="login-wrapper">
                    <div class="container">
                        <div class="loginbox">
                            <div class="login-right">
                                <div class="login-right-wrap">
                                    <h1>Loan Application</h1>
                                    <p class="account-subtitle">Fill in your details below</p>
                                    <div id="response-message"></div>

                                    <form id="loanForm" method="post">
                                        <!-- Gender -->
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <select name="Gender" id="gender" class="form-control" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        <!-- Married -->
                                        <div class="form-group">
                                            <label for="married">Married</label>
                                            <select name="Married" id="married" class="form-control" required>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>

                                        <!-- Dependents -->
                                        <div class="form-group">
                                            <label for="dependents">Dependents</label>
                                            <select name="Dependents" id="dependents" class="form-control" required>
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3+">3+</option>
                                            </select>
                                        </div>

                                        <!-- Education -->
                                        <div class="form-group">
                                            <label for="education">Education</label>
                                            <select name="Education" id="education" class="form-control" required>
                                                <option value="Graduate">Graduate</option>
                                                <option value="Not Graduate">Not Graduate</option>
                                            </select>
                                        </div>

                                        <!-- Self Employed -->
                                        <div class="form-group">
                                            <label for="self_employed">Self Employed</label>
                                            <select name="Self_Employed" id="self_employed" class="form-control" required>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>

                                        <!-- Applicant Income -->
                                        <div class="form-group">
                                            <label for="applicant_income">Applicant Income</label>
                                            <input type="number" name="ApplicantIncome" id="applicant_income" class="form-control" placeholder="Applicant Income" required>
                                        </div>

                                        <!-- Coapplicant Income -->
                                        <div class="form-group">
                                            <label for="coapplicant_income">Coapplicant Income</label>
                                            <input type="number" name="CoapplicantIncome" id="coapplicant_income" class="form-control" placeholder="Coapplicant Income" required>
                                        </div>

                                        <!-- Loan Amount -->
                                        <div class="form-group">
                                            <label for="loan_amount">Loan Amount</label>
                                            <input type="number" name="LoanAmount" id="loan_amount" class="form-control" placeholder="Loan Amount" required>
                                        </div>

                                        <!-- Loan Amount Term -->
                                        <div class="form-group">
                                            <label for="loan_amount_term">Loan Amount Term</label>
                                            <input type="number" name="Loan_Amount_Term" id="loan_amount_term" class="form-control" placeholder="Loan Amount Term (in months)" required>
                                        </div>

                                        <!-- Credit History -->
                                        <div class="form-group">
                                            <label for="credit_history">Credit History</label>
                                            <select name="Credit_History" id="credit_history" class="form-control" required>
                                                <option value="1.0">Good (1.0)</option>
                                                <option value="0.0">Bad (0.0)</option>
                                            </select>
                                        </div>

                                        <!-- Property Area -->
                                        <div class="form-group">
                                            <label for="property_area">Property Area</label>
                                            <select name="Property_Area" id="property_area" class="form-control" required>
                                                <option value="Urban">Urban</option>
                                                <option value="Semiurban">Semiurban</option>
                                                <option value="Rural">Rural</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-success" id="submitBtn" type="button">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include("include/footer.php"); ?>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#submitBtn").click(function (event) {
                event.preventDefault();

                // Collect form data
                var formData = {
                    Gender: $("#gender").val(),
                    Married: $("#married").val(),
                    Dependents: $("#dependents").val(),
                    Education: $("#education").val(),
                    Self_Employed: $("#self_employed").val(),
                    ApplicantIncome: parseFloat($("#applicant_income").val()),
                    CoapplicantIncome: parseFloat($("#coapplicant_income").val()),
                    LoanAmount: parseFloat($("#loan_amount").val()),
                    Loan_Amount_Term: parseFloat($("#loan_amount_term").val()),
                    Credit_History: parseFloat($("#credit_history").val()),
                    Property_Area: $("#property_area").val()
                };

                // Send data to FastAPI endpoint
                $.ajax({
                    url: "http://127.0.0.1:8000/predict", // Replace with your FastAPI endpoint
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(formData),
                    success: function (response) {
                        // Handle success response
                        $("#response-message").html(
                            `<p class='alert alert-success'>Loan Status: ${response["Loan Status"]}</p>`
                        );
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        $("#response-message").html(
                            `<p class='alert alert-danger'>Error: ${xhr.responseText}</p>`
                        );
                    }
                });
            });
        });
    </script>
</body>

</html>