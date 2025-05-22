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
  $("#dropdown button").each(function (index) {
    $(this).click(function () {
      let status = index === 0 ? "unanswered" : "answered";
      $("#dropdown").addClass("hidden");
      sortQuestions(status);
    });
  });

  // Sorting function
  function sortQuestions(status) {
    let questionBox = $("#questionBox");
    let questions = $(".question").toArray();

    questions.sort((a, b) => {
      let aHasAnswers = $(a).find("li:nth-child(2)").length > 0;
      let bHasAnswers = $(b).find("li:nth-child(2)").length > 0;

      return status === "answered" ? bHasAnswers - aHasAnswers : aHasAnswers - bHasAnswers;
    });

    questionBox.empty().append(questions);
  }

  let currentModuleId = "module-1"; // Default active module

  loadModules();

  function loadModules() {
    $("#moduleBox").html(`<p class="text-center text-gray-500">Loading...</p>`);

    $.ajax({
      url: "app/modules.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        let modulesHtml = "";
        if (data.length > 0) {
          data.forEach((module) => {
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

          currentModuleId = `module-${data[0].code}`;
          moduleTitleChange(data[0].name);
          tutorNameChange(data[0].tutor);
          loadQuestions(data[0].code);
        } else {
          modulesHtml = `<p class="text-center text-gray-500">No modules available.</p>`;
        }

        $("#moduleBox").html(modulesHtml);
        $("#" + currentModuleId).addClass("moduleactives");
      },
      error: function () {
        $("#moduleBox").html(`<p class="text-center text-red-500">Error loading modules.</p>`);
      },
    });
  }

  $(document).on("click", "button[id^='module-']", function () {
    currentModuleId = $(this).attr("id");
    let moduleTitle = $(this).attr("data-name");
    let tutorName = $(this).attr("data-tutor");

    $("button[id^='module-']").removeClass("moduleactives");
    $(this).addClass("moduleactives");

    let moduleCode = currentModuleId.split("-")[1];
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
                    
                    <div class="flex justify-between mt-3">
                    <span class="like-count">${question.likes ? question.likes+" voted" : ''}</span>
                      <button 
                        type="button" 
                        class="answerButton text-sm text-blue-600 hover:underline px-2 py-1 rounded border border-blue-400 hover:bg-blue-50 transition"
                        data-question-id="${question.id}">
                        Answer
                      </button>
                    </div>
                  </div>
                </div>

                ${question.answers.map(answer => `
                  <div class="flex items-start gap-3 bg-gray-100 rounded-lg p-3 mt-3 ms-12 relative">
                    <img src="images/staff.png" alt="${answer.staff}" class="w-9 h-9 rounded-full object-cover" />
                    <div class="flex-1">
                      <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-800">${answer.staff}</span>
                        <span class="text-xs text-gray-400">${answer.editStatus || ""}</span>
                      </div>
                      <p class="text-gray-700 mt-1">${answer.answer}</p>
                        ${answer.staff === sessionUsername ? `
                          <button 
                            class="editBtn flex items-center gap-1 text-sm text-gray-500 hover:text-yellow-600 transition mt-3"
                            data-answer="${answer.answer}"
                            data-id="${answer.id}">
                            <i class="fa-solid fa-pen"></i>
                            <span>Edit</span>
                          </button>` : ''
                        }
                    </div>
                  </div>
                `).join('')}
              </li>
            `;

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
      },
      error: function () {
        $("#questionBox").html(`<p class="text-center text-red-500">Error loading questions.</p>`);
      },
    });
  }


  // ------------------------------------------------------------------------------


   // Show Edit modal
   $(document).on("click",".editBtn", function(){
    const questionId = $(this).data("question-id");
    $("#submiteditAnswerBtn").data("question-id", questionId);
    $("#editanswer").val($(this).data("answer"));
    $("#editanswerid").val($(this).data("id"));
    $("#addeditanswermodal").removeClass("hidden");

    setTimeout(() => {
    $("#editanswer").focus().select();
    }, 100);

  })

 // Hide eidt modal when clicking close button
 $("#closeeditmodal").click(function () {
   $("#addeditanswermodal").addClass("hidden");
 });

 // Hide edit modal when clicking outside the modal content
 $("#addeditanswermodal").click(function (event) {
   if ($(event.target).closest(".bg-white").length === 0) {
     $("#addeditanswermodal").addClass("hidden");
   }
 });

  // ------------------------------------------------------------------------------


    // Show modal
    $("#addmodule").click(function () {
      $("#addmodulemodal").removeClass("hidden");
      setTimeout(() => {
        $("#modulename").focus().select();
      }, 100);
    });
  
    // Hide modal when clicking close button
    $("#closeModal").click(function () {
      $("#addmodulemodal").addClass("hidden");
    });
  
    // Hide modal when clicking outside the modal content
    $("#addmodulemodal").click(function (event) {
      if ($(event.target).closest(".bg-white").length === 0) {
        $("#addmodulemodal").addClass("hidden");
      }
    });
  
    // Hide modal when pressing the Escape key
    $(document).keydown(function (event) {
      if (event.key === "Escape") {
        $("#addmodulemodal").addClass("hidden");
      }
    });

  // ------------------------------------------------------------------------------


  $("#addmoduleForm").submit(function (event) {
    event.preventDefault();

    let modulename = $("#modulename").val().trim();
    if (modulename === "") {
      flashMessage("Please enter a module.",response.error);
      return;
    }

    $.ajax({
      url: "app/addmodule.php",
      method: "POST",
      data: { modulename: modulename },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Error parsing JSON:", e);
        }
        flashMessage(response.message,response.success);
        $("#modulename").val("");
        $("#addmodulemodal").addClass("hidden");
        loadModules();
      },
      error: function () {
        flashMessage("Error submitting question.","error");
      },
    });
  });

  // ------------------------------------------------------------------------------

  // Show Answer Modal
  $(document).on("click", ".answerButton", function () {
    let questionId = $(this).data("question-id");
    $("#submitAnswerBtn").data("question-id", questionId);
    $("#answerModal").removeClass("hidden");

    setTimeout(() => {
      $("#answer").focus().select();
      }, 100);
  });

  // Hide Answer Modal
  $("#closeanswerModal").click(function () {
    $("#answerModal").addClass("hidden");
  });

   // Hide modal when clicking outside the modal content
   $("#answerModal").click(function (event) {
    if ($(event.target).closest(".bg-white").length === 0) {
      $("#answerModal").addClass("hidden");
    }
  });


  // Hide modal when pressing the Escape key
  $(document).keydown(function (event) {
    if (event.key === "Escape") {
      $("#answerModal").addClass("hidden");
    }
  });

  // ------------------------------------------------------------------------------


  $("#answerForm").on("submit", function (event) {
    event.preventDefault();
    const questionId = $("#submitAnswerBtn").data("question-id");
    const answer = $("#answer").val().trim();

    if (answer !== "") {      
      submitAnswer(questionId, answer);
    } else {
      flashMessage("Please write an answer before submitting.","error");
    }
  });

  function submitAnswer(questionId, answer) {
    
    let moduleCode = currentModuleId.split("-")[1];

    $.ajax({
      url: "app/answerquestion.php",
      method: "POST",
      data: { questionId: questionId, answer: answer },
      success: function (response) {
        try {
          response = JSON.parse(response);
        } catch (e) {
          console.error("Error parsing JSON:", e);
        }
        flashMessage(response.message,response.success);
        $("#answer").val("");
        $("#answerModal").addClass("hidden");
        loadQuestions(moduleCode);
      },
      error: function () {
        flashMessage("Error submitting question.",error);
      },
    });
}


  $("#editanswerform").on("submit", function (event) {
    event.preventDefault();

    // const questionId = $("#submiteditAnswerBtn").data("question-id");
    const editanswer = $("#editanswer").val().trim();
    const answerId = $("#editanswerid").val().trim();

    if (editanswer !== "") {      
      submiteditAnswer( editanswer, answerId);
    } else {
      flashMessage("Please write an answer before submitting.","error");
    }
  });


function submiteditAnswer( editanswer, answerId) {
    
  let moduleCode = currentModuleId.split("-")[1];

  $.ajax({
    url: "app/answerquestion.php",
    method: "POST",
    data: { editanswer: editanswer, answerId : answerId },
    success: function (response) {
      try {
        response = JSON.parse(response);
      } catch (e) {
        console.error("Error parsing JSON:", e);
      }
      flashMessage(response.message,response.success);
      $("#editanswer").val("");
      $("#addeditanswermodal").addClass("hidden");
      loadQuestions(moduleCode);
    },
    error: function () {
      flashMessage("Error submitting question.",error);
    },
  });
}

});
