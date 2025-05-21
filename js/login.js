// Login Function

$("#loginForm").submit(function (event) {
  event.preventDefault();

  let username = $("#username").val().trim();
  let password = $("#password").val().trim();

  if (username === "" || password === "") {
    flashMessage("Please enter username and password.");
    return;
  }

  $.ajax({
    url: "app/login.php",
    method: "POST",
    data: { username: username, password: password },
    dataType: "json",
    success: function (response) {
      if (response.success === "success") {
        flashMessage("Login successful as " + response.role);
        
        window.location.href =
          response.role === "staff" ? "staffindex.php" : "index.php";
      } else {
        console.log(response);
        flashMessage(response.message,response.success);
      }
    },
    error: function () {
      flashMessage("Error logging in.","error");
    },
  });
});
