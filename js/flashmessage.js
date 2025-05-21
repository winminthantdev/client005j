function flashMessage(message, type = "success", duration = 3000) {
  // Remove any existing flash messages
  $("#flashMessage").remove();

  // Define colors based on the message type
  let bgColor = type === "error" ? "bg-red-100 text-red-900" : "bg-teal-100 text-teal-900";
  let barColor = type === "error" ? "bg-red-500" : "bg-teal-500";

  // Create flash message HTML
  let flashHtml = `
    <div id="flashMessage" class="fixed top-4 right-4 z-50 w-[300px]">
      <div class="${bgColor} rounded-lg px-4 py-3 shadow-md relative overflow-hidden">
        <p class="font-semibold">${type === "error" ? "Error!" : "Success!"}</p>
        <p class="text-sm mt-1">${message}</p>
        <div class="absolute bottom-0 left-0 h-1 ${barColor}" style="width: 100%; animation: flashProgress ${duration}ms linear forwards;"></div>
      </div>
    </div>`;

  // Append to body
  $("body").append(flashHtml);

  // Remove after duration
  setTimeout(() => {
    $("#flashMessage").fadeOut(500, function () {
      $(this).remove();
    });
  }, duration);
}
