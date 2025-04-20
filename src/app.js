function capitalizeFirst(string) {
  if (string.length === 0) {
    return "";
  }
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function greetUser() {
  const hour = new Date().getHours();
  if (hour > 0 && hour < 10) {
    return "Good morning";
  } else if (hour > 10 && hour < 18) {
    return "Good afternoon";
  } else {
    return "Good evening";
  }
}

$(document).ready(function () {
  //   localStorage.clear();
  const storedUserJSON = localStorage.getItem("currentUser");
  const $loginFormContainer = $("#loginFormContainer");
  const $usernameElement = $("#username");

  function showLoginForm() {
    $loginFormContainer.removeClass("hidden").addClass("flex");
  }

  function hideLoginForm() {
    $loginFormContainer.addClass("hidden").removeClass("flex");
  }

  if (storedUserJSON) {
    const storedUser = JSON.parse(storedUserJSON);
    let greetText = greetUser();
    $usernameElement.text(`${greetText}, ${storedUser}!`);
    hideLoginForm();
  } else {
    showLoginForm();
    $usernameElement.text("Welcome!");
  }

  const $loginForm = $loginFormContainer.find("form");
  $loginForm.on("submit", function (event) {
    event.preventDefault();
    const $usernameInput = $(this).find('input[type="text"]');
    const username = capitalizeFirst($usernameInput.val().trim());

    if (username) {
      let serialized_data = JSON.stringify(username, null, 2);
      localStorage.setItem("currentUser", serialized_data);

      let greetText = greetUser();
      $usernameElement.text(`${greetText}, ${username}!`);
      hideLoginForm();
    }
  });
});
