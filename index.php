<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}else if($_SESSION["user"]["role"] !== "student"){
  header("Location: staffindex.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Q & A Web App</title>

    <!-- fav icon -->
    <link rel="shortcut icon" href="images/sitelogo.png" type="image/x-icon" />

    <!-- fontawesome cnd link 1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- tailwind css cdn link 1 -->
    <script src="https://cdn.tailwindcss.com"></script>
     <!-- <script src="tailwindcss.js"></script> -->

    <!-- custom css 1 js 1 -->
    <link href="js/jquery-ui-1.13.2.custom/jquery-ui.min.css" rel="stylesheet" type="text/css" />

    <!-- custom css 1 js 1 -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <!-- Start Top Navbar -->
    <nav class="w-full shadow px-12 py-4">
      <div class="container mx-auto">
        <div class="flex justify-between items-center">
          <h3 class="text-3xl font-bold text-green-600">My Room</h3>
          <button
            type="button"
            class="text-red-500 font-bold rounded-md cursor-pointer px-4 py-2 hover:text-white hover:bg-red-700 transition"
          >
            <a href="logout.php">Logout</a>
          </button>
        </div>
      </div>
    </nav>
    <!-- End Top Navbar -->

    <!-- Start Main Section -->
    <div class="h-[70vh] container mx-auto">
      <div class="flex">
        <div id="moduleContainer" class="w-full h-[90vh] flex flex-col items-center py-8">
          <div id="moduleBox" class="flex flex-wrap mx-2">
            <!-- module will be loaded here dynamically -->
          </div>
        </div>
        <div id="questionContainer" class="w-full hidden">
          <div class="w-full relative backdrop-blur-sm">
            <div class="w-full flex justify-between items-center space-x-4 border-b-4 px-8 py-4" >
              <div id="backToModule" class="cursor-pointer text-gray-500 hover:text-gray-700" >
                <i class="fa-solid fa-angle-left"></i>
              </div>
              <div class="w-full flex flex-col">
                <span id="moduleTitle" class="font-bold"></span>
                <span id="tutorName" class="text-xs font-bold"></span>
              </div>
              <div class="relative inline-block">
                <!-- Menu Icon -->
                <div id="menu" class="cursor-pointer text-gray-500 hover:text-gray-700 px-2">
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>

                <!-- Dropdown Menu (Initially Hidden) -->
                <div id="dropdown" class="hidden p-2 bg-white shadow absolute top-8 right-0 w-60 rounded-md border z-40">
                  <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Sort by unanswered</button>
                  <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Sort by answered</button>
                </div>
              </div>


            </div>

            <div id="" class="h-[75vh] overflow-y-auto space-y-8 px-4 py-12" >

              <div class="absolute right-0 bottom-0">
                <div id="askModal" class="bg-green-500 text-white shadow cursor-pointer rounded-md hover:bg-green-600 transition px-4 py-2">Ask Now</div>             
              </div>

              <div id="questionContainer">
                <ul id="questionBox" class="">
                  <!-- questions will be loaded here dynamically -->
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Main Section -->


<!-- Start Modal box for ask question -->
 <div id="addaskmodal" class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
    <button id="closeaskModal" class="absolute top-1 right-2 text-3xl text-gray-500 hover:text-red-700">
      &times;
    </button>
    <h3 class="text-lg font-semibold mb-4">Ask Qusetion</h3>    
    <form id="askquestionForm" action="" method="POST">
      <div class="mb-4">
        <label for="askquestion" class="block text-sm font-medium text-gray-700">Qusetion</label>
        <input type="text" name="askquestion" id="askquestion" placeholder="Ask question"
          class="w-full mt-1 p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" />
      </div>
      <button type="submit" id="" class="w-full bg-green-600 text-white p-2 rounded-md hover:bg-green-700">
        Submit Question
      </button>
    </form>
  </div>
</div>
<!-- End Modal box for ask question -->

<!-- Start Modal box for edit -->
<div id="addeditquestionmodal" class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
    <button id="closeeditmodal" class="absolute top-1 right-2 text-3xl text-gray-500 hover:text-red-700">
      &times;
    </button>
    <h3 class="text-lg font-semibold mb-4">Ask Qusetion</h3>    
    <form id="editaskquestionForm" action="" method="POST">
      <div class="mb-4">
        <label for="editaskquestion" class="block text-sm font-medium text-gray-700">Qusetion</label>
        <input type="text" name="editaskquestion" id="editaskquestion" placeholder="Edit question"
          class="w-full mt-1 p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500" value="" />
        <input type="hidden" id="editquestionid" name="editquestionid" />
      </div>
      <button type="submit" id="" class="w-full bg-green-600 text-white p-2 rounded-md hover:bg-green-700">
        Update
      </button>
    </form>
  </div>
</div>
<!-- End Modal box for edit -->


    <!-- jquery js 1 -->
    <script src="js/jquery/jquery-3.7.1.min.js" type="text/javascript"></script>

    <!-- jquery ui css1 js 1 -->
    <script
      src="js/jquery-ui-1.13.2.custom/jquery-ui.min.js"
      type="text/javascript"
    ></script>

    <!--  flashmessage.js file -->
    <script src="js/flashmessage.js"></script>

    <script>
        const sessionUsername = "<?php echo $_SESSION['user']['username']; ?>";
        const sessionRole = "<?php echo $_SESSION['user']['role']; ?>";
    </script>

    <!-- custom css 1 js 1 -->
    <script src="js/studentapp.js" type="text/javascript"></script>
  </body>
</html>
