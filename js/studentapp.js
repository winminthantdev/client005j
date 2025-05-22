$(document).ready(function () {

  function mobileRespone(){
    $("#questionContainer").removeClass("hidden");
    $("#moduleContainer").addClass("hidden");
  }

  $("#backToModule").click(function(){
    $("#questionContainer").addClass("hidden");
    $("#moduleContainer").removeClass("hidden");
  })

  $("#menu").click(function (event) {
    event.stopPropagation();
    $("#dropdown").toggleClass("hidden");
  });

  // Event listener for sorting
  document.querySelectorAll("#dropdown button").forEach((button, index) => {
    button.addEventListener("click", function () {
      let status = index === 0 ? "unanswered" : "answered";
      sortQuestions(status);
      $("#dropdown").addClass("hidden");
    });
  });

  // ------------------------------------------------------------------------------

  // Sorting function
  function sortQuestions(status) {
    let questionBox = document.getElementById("questionBox");
    let questions = Array.from(questionBox.getElementsByClassName("question"));

    questions.sort((a, b) => {
      let aHasAnswers = a.querySelector("li:nth-child(2)") ? true : false;
      let bHasAnswers = b.querySelector("li:nth-child(2)") ? true : false;

      if (status === "answered") {
        return bHasAnswers - aHasAnswers;
      } else {
        return aHasAnswers - bHasAnswers;
      }
    });

    questionBox.innerHTML = ""; // Clear and re-add sorted questions
    questions.forEach((question) => questionBox.appendChild(question));
  }


  loadModules();

  // ------------------------------------------------------------------------------

  function loadModules() {

    $("#moduleBox").html(`<p class="text-center text-gray-500">Loading...</p>`);


    $.ajax({
      url: "app/modules.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        let modulesHtml = "";
        if (data.length > 0) {
          data.forEach((module, index) => {
            modulesHtml += `
                <div class="w-full sm:w-1/2 lg:w-1/3 p-2">
              <button 
                type="button" 
                id="module-${module.code}" 
                class="group w-full text-left bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg transition duration-200 p-4 focus:outline-none focus:ring-2 focus:ring-green-500" 
                data-name="${module.name}" 
                data-tutor="${module.tutor}">
                
                <div class="flex items-center space-x-4">
                  <div class="bg-green-100 text-green-600 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.25a12.083 12.083 0 01-6.16-10.672L12 14z" />
                    </svg>
                  </div>

                  <div class="overflow-hidden">
                    <h3 class="text-lg font-bold text-gray-800 truncate">${module.name}</h3>
                    <p class="text-sm text-gray-500 truncate">Tutor: ${module.tutor}</p>
                  </div>
                </div>
              </button>
            </div>
            `;
          });

          activeModuleId = `module-${data[0].id}`;
          moduleTitleChange(data[0].name);
          tutorNameChange(data[0].tutor);
          loadQuestions(data[0].id);
        } else {
          modulesHtml = `<p class="text-center text-gray-500">No modules available.</p>`;
        }

        $("#moduleBox").html(modulesHtml);
        // $("#moduleBox button:first").trigger("click");
      },
      error: function () {
        $("#moduleBox").html(
          `<p class="text-center text-red-500">Error loading modules.</p>`
        );
      },
    });
  }

  // ------------------------------------------------------------------------------


  $(document).on("click", "button[id^='module-']", function () {
    activeModuleId = $(this).attr("id");
    let moduleTitle = $(this).attr("data-name");
    let tutorName = $(this).attr("data-tutor");


    $("button[id^='module-']").removeClass("moduleactives");
    $(this).addClass("moduleactives");

    let moduleCode = activeModuleId.split("-")[1];
    loadQuestions(moduleCode);
    moduleTitleChange(moduleTitle);
    tutorNameChange(tutorName);
    mobileRespone();
  });

  function moduleTitleChange(moduleTitle) {
    $("#moduleTitle").text(moduleTitle);
  }

  function tutorNameChange(tutorName) {
    $("#tutorName").text(tutorName);
  }


  // ------------------------------------------------------------------------------


  function loadQuestions(moduleCode) {
    $("#questionBox").html(`<p class="text-center text-gray-500">Loading...</p>`);
  
    $.ajax({
      url: "app/questions.php",
      method: "GET",
      data: { moduleCode: moduleCode },
      dataType: "json",
      success: function (data) {
        let questionHtml = "";
        let hasQuestion = false;
  
        if (data.length > 0) {
          data.forEach((question) => {
            if (question.moduleCode == moduleCode) {
              hasQuestion = true;
  
              let questionGroup = "";

              questionGroup = `
              <li class="max-w-2xl mx-auto mb-6">
                <div class="flex items-start gap-3 bg-white shadow-md rounded-lg p-4">
                  <img src="images/student.png" alt="${question.username}" class="w-10 h-10 rounded-full object-cover" />
                  <div class="flex-1">
                    <div class="flex justify-between items-center">
                      <span class="font-semibold text-sm text-gray-800">${question.username}</span>
                      <span class="text-xs text-gray-400">${question.editStatus || ""}</span>
                    </div>
                    <p class="text-gray-700 my-1">${question.question}</p>

                    <div class="flex items-center gap-4 text-sm text-gray-500 mt-2">
                      <button class="like-button flex items-center gap-1 hover:text-blue-600" data-id="${question.id}">
                        <i class="fa-regular fa-thumbs-up"></i> <span class="like-count">${question.likes}</span>
                      </button>
                      <button class="disklike-button flex items-center gap-1 hover:text-red-600" data-id="${question.id}">
                        <i class="fa-regular fa-thumbs-down"></i>
                      </button>

                      ${question.username === sessionUsername ? `
                        <button id="editBtn" 
                                class="flex items-center gap-1 px-2 py-1 rounded hover:text-yellow-600 text-gray-500 text-sm" 
                                data-question="${question.question}" 
                                data-id="${question.id}">
                          <i class="fa-solid fa-pen"></i> <span>Edit</span>
                        </button>` : ''
                      }                      
                    </div>
                  </div>
                </div>

                ${question.answers.map(answer => `
                  <div class="flex items-start gap-3 bg-gray-100 rounded-lg p-3 mt-3 ms-12">
                    <img src="images/staff.png" alt="${answer.staff}" class="w-9 h-9 rounded-full object-cover" />
                    <div>
                      <span class="text-sm font-semibold text-gray-800">${answer.staff}</span>
                      <p class="text-gray-700 mt-1">${answer.answer}</p>
                    </div>
                  </div>
                `).join('')}
              </li>`;

  
              questionHtml += `<div class="question w-auto lg:pe-24 mb-4" data-status="${question.status}">${questionGroup}</div>`;
              
            }
          });
  
          if (!hasQuestion) {
            questionHtml = `<p class="text-center text-gray-500">No questions available for this module.</p>`;
          }
        } else {
          questionHtml = `<p class="text-center text-gray-500">No questions available.</p>`;
        }
  
        $("#questionBox").html(questionHtml);
  
        // Fetch votes for all questions after they load
        if ($(".like-button").length) {
          $(".like-button, .disklike-button").each(function () {
            let questionId = $(this).data("id");
            fetchUserVote(questionId);
          });
        }


      },
      error: function () {
        $("#questionBox").html(
          `<p class="text-center text-red-500">Error loading questions.</p>`
        );
      },
    });
  }
  
  // ------------------------------------------------------------------------------

  $("#askquestionForm").submit(function (event) {
    event.preventDefault();

    let questionText = $("#askquestion").val().trim();
    if (questionText === "") {
      flashMessage("Please enter a question.","error");
      return;
    }

    let moduleCode = activeModuleId.split("-")[1];

    $.ajax({
      url: "app/askquestion.php",
      method: "POST",
      data: { question: questionText, moduleCode: moduleCode },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Error parsing JSON:", e);
        }
        flashMessage(response.message,response.success);
        $("#askquestion").val("");
        $("#addaskmodal").addClass("hidden");
        loadQuestions(moduleCode);
      },
      error: function () {
        flashMessage("Error submitting question.","error");
      },
    });
  });

  // ------------------------------------------------------------------------------

  $("#editaskquestionForm").submit(function (event) {
    event.preventDefault();

    let questionText = $("#editaskquestion").val().trim();
    let questionId = $("#editquestionid").val();

    if (questionText === "") {
      flashMessage("Please enter a question.","error");
      return;
    }

    let moduleCode = activeModuleId.split("-")[1];

    $.ajax({
      url: "app/askquestion.php",
      method: "POST",
      data: { 
        question: questionText,
        moduleCode: moduleCode,
        questionId: questionId,
       },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Error parsing JSON:", e);
        }
        flashMessage(response.message,response.success);
        $("#editaskquestion").val("");
        $("#addeditquestionmodal").addClass("hidden");
        loadQuestions(moduleCode);
      },
      error: function () {
        flashMessage("Error submitting question.","error");
      },
    });
  });

  // ------------------------------------------------------------------------------


  function fetchUserVote(questionId) {
    $.ajax({
      url: "app/vote.php",
      method: "GET",
      data: { questionId: questionId },
      success: function (response) {
        let voteData = JSON.parse(response);
        let likeBtn = $(".like-button[data-id='" + questionId + "']");
        let dislikeBtn = $(".disklike-button[data-id='" + questionId + "']");

        if (voteData.voteStatus === 1) {
          likeBtn.addClass("voteds");
          dislikeBtn.removeClass("voteds");
        } else if (voteData.voteStatus === -1) {
          dislikeBtn.addClass("voteds");
          likeBtn.removeClass("voteds");
        } else {
          likeBtn.removeClass("voteds");
          dislikeBtn.removeClass("voteds");
        }
      },
      error: function () {
        console.log("Error fetching user vote.");
      },
    });
  }

  // ------------------------------------------------------------------------------

  function updateVote(questionId, voteStatus) {
    $.ajax({
      url: "app/vote.php",
      method: "POST",
      data: { questionId: questionId, voteStatus: voteStatus },
      success: function (response) {
        let res = JSON.parse(response);
        if (res.success) {
          fetchUserVote(questionId);
          updateLikeCount(questionId, res.voteChange);
        }
      },
      error: function () {
        console.log("Error updating vote.");
      },
    });
  }

  // ------------------------------------------------------------------------------

  function updateLikeCount(questionId, change) {
    let likeCountSpan = $(".like-button[data-id='" + questionId + "']").find(".like-count");
    let currentLikes = parseInt(likeCountSpan.text());
    likeCountSpan.text(currentLikes + change);
  }

  // ------------------------------------------------------------------------------

  $(document).on("click", ".like-button", function () {
    let questionId = $(this).data("id");
    let isLiked = $(this).hasClass("voteds");
    let voteStatus = isLiked ? 0 : 1;
    updateVote(questionId, voteStatus);
  });

  $(document).on("click", ".disklike-button", function () {
    let questionId = $(this).data("id");
    let isDisliked = $(this).hasClass("voteds");
    let voteStatus = isDisliked ? 0 : -1;
    updateVote(questionId, voteStatus);
  });
});

  // ------------------------------------------------------------------------------


   // Show Edit modal
   $(document).on("click","#editBtn", function(){
     $("#editaskquestion").val($(this).data("question"));
     $("#editquestionid").val($(this).data("id"));
     $("#addeditquestionmodal").removeClass("hidden");

     setTimeout(() => {
        $("#editaskquestion").focus().select();
      }, 100);

   })

  // Hide eidt modal when clicking close button
  $("#closeeditmodal").click(function () {
    $("#addeditquestionmodal").addClass("hidden");
  });

  // Hide edit modal when clicking outside the modal content
  $("#addeditquestionmodal").click(function (event) {
    if ($(event.target).closest(".bg-white").length === 0) {
      $("#addeditquestionmodal").addClass("hidden");
    }
  });



  // ------------------------------------------------------------------------------

   // Show ask modal
   $("#askModal").click(function () {
    $("#addaskmodal").removeClass("hidden");
    $("#askquestion").focus();
  });

  // Hide ask modal when clicking close button
  $("#closeaskModal").click(function () {
    $("#addaskmodal").addClass("hidden");
  });

  // Hide ask modal when clicking outside the modal content
  $("#addaskmodal").click(function (event) {
    if ($(event.target).closest(".bg-white").length === 0) {
      $("#addaskmodal").addClass("hidden");
    }
  });

  // Hide ask modal when pressing the Escape key
  $(document).keydown(function (event) {
    if (event.key === "Escape") {
      $("#addaskmodal").addClass("hidden");
    }
  });
