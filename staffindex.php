<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}else if($_SESSION["user"]["role"] !== "staff"){
  header("Location: index.php");
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
  <link rel="shortcut icon" href="/images/sitelogo.png" type="image/x-icon" />

  <!-- fontawesome cnd link 1 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- tailwind css cdn link 1 -->
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->
  <script src="tailwindcss.js"></script>


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
        <h3 class="text-3xl font-bold text-green-500">My Room</h3>
        <button type="button" class="text-red-500 font-bold rounded-md cursor-pointer px-4 py-2 hover:text-white hover:bg-red-700 transition" >
          <a href="logout.php">Logout</a>
        </button>
      </div>
    </div>
  </nav>
  <!-- End Top Navbar -->

  <!-- Start Main Section -->
  <div class="h-[70vh] container mx-auto ">
    <div class="flex">
      <div id="moduleContainer" class="w-full h-[90vh] flex flex-col items-center py-8 relative">
        <div class="absolute right-0 bottom-16 px-4">
          <button type="button" id="addmodule" class="w-full bg-green-500 hover:bg-green-600 text-white rounded-md px-4 py-2">
            Add Module
          </button>
        </div>
        <div id="moduleBox" class="flex flex-wrap mx-2">
        <!-- module will be loaded here dynamically -->
        </div>
      </div>
      <div id="questionContainer" class="w-full hidden">
        <div class="w-full relative backdrop-blur-sm">
          <div class="w-full flex justify-between items-center space-x-4 border-b-4 px-8 py-4">
            <div id="backToModule" class="cursor-pointer text-gray-500 hover:text-gray-700">
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
                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                  Sort by unanswered
                </button>
                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                  Sort by answered
                </button>
              </div>
            </div>
          </div>

          <div id="" class="h-[75vh] space-y-8 overflow-y-auto px-4 py-12">

            <div id="questionContainer">
              <ul id="questionBox">
                <!-- questions will be loaded here dynamically -->
              </ul>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
  <!-- End Main Section -->


<!-- Answer Modal -->
<div id="answerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-6 relative animate-fadeIn">
    <button id="closeanswerModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
      <i class="fa-solid fa-xmark text-lg"></i>
    </button>
    <h2 class="text-xl font-semibold text-green-600 mb-4">Answer Question</h2>
    <form id="answerForm" method="POST">
      <div class="space-y-4">
        <div>
          <label for="answer" class="block text-sm font-medium text-gray-700">Your Answer</label>
          <textarea name="answer" id="answer" rows="4" placeholder="Write your reply here..."
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none resize-none"></textarea>
        </div>
        <button type="submit" id="submitAnswerBtn"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition">
          Submit Answer
        </button>
      </div>
    </form>
  </div>
</div>



<!-- Add Module Modal -->
<div id="addmodulemodal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-6 relative animate-fadeIn">
    <button id="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500">
      <i class="fa-solid fa-xmark text-lg"></i>
    </button>
    <h2 class="text-xl font-semibold text-green-600 mb-4">Add New Module</h2>
    <form id="addmoduleForm" method="POST">
      <div class="space-y-4">
        <div>
          <label for="modulename" class="block text-sm font-medium text-gray-700">Module Name</label>
          <input type="text" name="modulename" id="modulename" placeholder="Enter module name"
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none" />
        </div>
        <button type="submit"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition">
          Add Module
        </button>
      </div>
    </form>
  </div>
</div>


<!-- Edit Answer Modal -->
<div id="addeditanswermodal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-6 relative animate-fadeIn">
    <button id="closeeditmodal" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 text-2xl leading-none">
      &times;
    </button>
    <h2 class="text-xl font-bold text-green-600 mb-4">Edit Your Answer</h2>
    <form id="editanswerform" method="POST">
      <div class="space-y-4">
        <div>
          <label for="editanswer" class="block text-sm font-medium text-gray-700">Answer</label>
          <textarea name="editanswer" id="editanswer" rows="4" placeholder="Update your answer here..."
            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none resize-none"></textarea>
          <input type="hidden" id="editanswerid" name="editanswerid" />
        </div>
        <button type="submit" id="submiteditAnswerBtn"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
          Update Answer
        </button>
      </div>
    </form>
  </div>
</div>


  <!-- jquery js 1 -->
  <script src="js/jquery/jquery-3.7.1.min.js" type="text/javascript"></script>

  <!-- jquery ui css1 js 1 -->
  <script src="js/jquery-ui-1.13.2.custom/jquery-ui.min.js" type="text/javascript"></script>

    <!--  flashmessage.js file -->
    <script src="js/flashmessage.js"></script>

    <script>
        const sessionUsername = "<?php echo $_SESSION['user']['username']; ?>";
        const sessionRole = "<?php echo $_SESSION['user']['role']; ?>";
    </script>
  <!-- custom css 1 js 1 -->
  <script src="js/staffapp.js" type="text/javascript"></script>

</body>

</html>