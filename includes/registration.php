<?php
//error_reporting(0);
if(isset($_POST['signup']))
{
    $fname = $_POST['fullname'];
    $email = $_POST['emailid']; 
    $mobile = $_POST['mobileno'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];

    // Server-side password validation
    if (!isPasswordStrong($password)) {
        echo "<script>alert('Password is not strong enough. Please enter a stronger password.');</script>";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "<script>alert('Password and Confirm Password do not match.');</script>";
        exit;
    }

    // Use Argon2 for password hashing
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);
    $sql = "INSERT INTO tblusers(FullName, EmailId, ContactNo, Password) VALUES(:fname, :email, :mobile, :password)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
        echo "<script>alert('Registration successful. Now you can login');</script>";
    }
    else 
    {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
}

// Password strength checking function
function isPasswordStrong($password) {
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/';
    return preg_match($pattern, $password);
}
?>

<script>
function checkAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check_availability.php",
        data: 'emailid=' + $("#emailid").val(),
        type: "POST",
        success: function(data) {
            $("#user-availability-status").html(data);
            $("#loaderIcon").hide();
        },
        error: function() {}
    });
}

function checkPasswordStrength() {
    var password = document.getElementById("password").value;
    var passwordStrength = document.getElementById("password-strength");
    var signupButton = document.getElementById("submit");

    // Check password strength
    var strength = getPasswordStrength(password);

    if (strength < 3) {
        passwordStrength.innerHTML = "Weak";
        passwordStrength.style.color = "red";
        signupButton.disabled = true;
    } else if (strength == 3) {
        passwordStrength.innerHTML = "Medium";
        passwordStrength.style.color = "orange";
        signupButton.disabled = true;
    } else {
        passwordStrength.innerHTML = "Strong";
        passwordStrength.style.color = "green";
        signupButton.disabled = false;
    }
}

function getPasswordStrength(password) {
    var strength = 0;

    if (password.length >= 12) {
        strength++;
    }

    if (/[a-z]/.test(password)) {
        strength++;
    }

    if (/[A-Z]/.test(password)) {
        strength++;
    }

    if (/\d/.test(password)) {
        strength++;
    }

    if (/[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/.test(password)) {
        strength++;
    }

    return strength;
} 

function checkPasswordMatch() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmpassword").value;
    var passwordStrength = document.getElementById("password-strength");
    var signupButton = document.getElementById("submit");

    if (password!== confirmPassword) {
        passwordStrength.innerHTML = "Passwords do not match.";
        signupButton.disabled = true;
        return false;
    }

    return true;
}

</script>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#signupform">Sign Up</button>
<div class="modal fade" id="signupform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-6">
              <form method="post" name="signup" onSubmit="return valid() && checkPassword();">
                <!-- form fields -->
                <div class="form-group">
                  <input type="text" class="form-control" name="fullname" placeholder="Full Name" required="required">
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="mobileno" placeholder="Mobile Number" maxlength="11" required="required">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkAvailability()" placeholder="Email Address" required="required">
                   <span id="user-availability-status" style="font-size:12px;"></span>
                </div>
                <div class="form-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="required" oninput="setTimeout(checkPasswordStrength, 500)">
                <small>Password must be at least 12 characters long and contain at least one capital letter, one symbol, and one number.</small>
                <span id="password-strength" style="font-size:12px;">
                </span>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" required="required">
            </div>
                <div class="form-group checkbox">
                  <input type="checkbox" id="terms_agree" required="required" checked="">
                  <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
                </div>
                <div class="form-group">
                  <input type="submit" value="Sign Up" name="signup" id="submit" class="btn btn-block">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Already got an account? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Login Here</a></p>
      </div>
    </div>
  </div>
</div>