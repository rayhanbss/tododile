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

function postNewUser(userData) {
  $.ajax({
    url: "../data/todos.json",
    type: "GET",
    dataType: "json",
    success: function (existingData) {
      // Add the new user to the users array
      if (!existingData.users) {
        existingData.users = [];
      }
      existingData.users.push(userData);

      // Save the updated data
      $.ajax({
        url: "../data/todos.json",
        type: "POST",
        dataType: "json",
        data: JSON.stringify(existingData),
        contentType: "application/json",
        success: function (response) {
          console.log("User created successfully:", response);
        },
        error: function (error) {
          console.error("Error creating user:", error);
        },
      });
    },
    error: function (error) {
      console.error("Error reading existing data:", error);
    },
  });
}

function getUserData(username) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "../data/todos.json",
      type: "GET",
      dataType: "json",
      success: function (data) {
        // Access the users array from the data object
        const users = data.users || [];
        const userData = users.find((user) => user.name === username);

        if (userData) {
          console.log(userData);
          resolve(userData);
        } else {
          console.log("User not found!, Creating new user...");
          const newUserData = { name: username, todos: [] };
          postNewUser(newUserData);
          resolve(newUserData);
        }
      },
      error: function (error) {
        console.error("Error fetching user data:", error);
        reject(error);
      },
    });
  });
}

$(document).ready(function () {
  // localStorage.clear();
  const userStored = JSON.parse(localStorage.getItem("currentUser"));
  const currentPage = window.location.pathname.split("/").pop();

  // Handle index.html (main page)
  if (currentPage === "index.html" || currentPage === "") {
    const $usernameElement = $("#username");

    if (userStored) {
      const username = userStored.name;
      let greetText = greetUser();
      $usernameElement.text(`${greetText}, ${username}!`);
      console.log(localStorage.getItem("currentUser"));

      // Create todo list container if it doesn't exist
      if ($("#todoListContainer").length === 0) {
        $("#addTaskContainer").after(
          '<div id="todoListContainer" class="w-full mt-8"></div>'
        );
      }
    } else {
      // No user found, redirect to login page
      window.location.href = "login.html";
    }
  }

  // Handle login.html page
  if (currentPage === "login.html") {
    if (userStored) {
      window.location.href = "index.html";
      return;
    }

    // Handle login form submission
    $("form").on("submit", function (event) {
      event.preventDefault();
      const $usernameInput = $(this).find('input[name="username"]');
      const username = capitalizeFirst($usernameInput.val().trim());

      if (username) {
        getUserData(username)
          .then((userData) => {
            // Store user data in localStorage
            localStorage.setItem("currentUser", JSON.stringify(userData));
            console.log(localStorage.getItem("currentUser"));
            // Redirect to main page
            window.location.href = "index.html";
          })
          .catch((error) => {
            console.error("Error fetching user data:", error);
          });
      }
    });
  }
});
