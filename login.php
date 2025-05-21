<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Q & A Web App</title>

    <!-- fav icon -->
    <link rel="shortcut icon" href="images/sitelogo.png" type="image/x-icon" />

    <!-- fontawesome cnd link 1 -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <!-- tailwind css cdn link 1 -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <script src="tailwindcss.js"></script>


    <!-- custom css 1 js 1 -->
    <link
      href="js/jquery-ui-1.13.2.custom/jquery-ui.min.css"
      rel="stylesheet"
      type="text/css"
    />

    <!-- custom css 1 js 1 -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg bg-white shadow-lg rounded-lg p-8">
      <h2 class="text-3xl font-bold text-center text-gray-800">
        Welcome Back!
      </h2>
      <p class="text-center text-gray-500">Login to your account</p>
      <form id="loginForm" class="mt-6" action="" method="POST">
        <div class="mb-4">
          <label for="username" class="block text-gray-700 mb-2">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your username"
            required
          />
        </div>
        <div class="mb-6">
          <label for="password" class="block text-gray-700 mb-2">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your password"
            required
          />
        </div>
        <button
          type="submit"
          class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg"
        >
          Login
        </button>
      </form>
    </div>

    <!-- jquery js 1 -->
    <script src="js/jquery/jquery-3.7.1.min.js" type="text/javascript"></script>

    <!-- jquery ui css1 js 1 -->
    <script
      src="js/jquery-ui-1.13.2.custom/jquery-ui.min.js"
      type="text/javascript"
    ></script>

    <!--  flashmessage.js file -->
    <script src="js/flashmessage.js"></script>


    <!-- custom css 1 login js 1 -->
    <script src="js/login.js" type="text/javascript"></script>
  </body>
</html>
