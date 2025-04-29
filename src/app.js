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

function postNewUser(username) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "http://localhost:3000/users",
      type: "GET",
      dataType: "json",
      success: function (existingUsers) {
        if (!existingUsers) {
          existingUsers = [];
        }
        const length = existingUsers.length;
        const userData = {
          id: length + 1,
          name: username,
        };

        $.ajax({
          url: "http://localhost:3000/users",
          type: "POST",
          dataType: "json",
          data: JSON.stringify(userData),
          contentType: "application/json",
          success: function (response) {
            console.log("User created successfully:", response);
            resolve(response);
          },
          error: function (error) {
            console.error("Error creating user:", error);
            reject(error);
          },
        });
      },
      error: function (error) {
        console.error("Error reading existing data:", error);
        reject(error);
      },
    });
  });
}

function getUserData(username) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `http://localhost:3000/users?name=${username}`,
      type: "GET",
      dataType: "json",
      success: function (data) {
        const userData = data[0] || null;
        console.log("User data fetched successfully:", userData);

        if (userData) {
          // Fetch todos for this user
          $.ajax({
            url: `http://localhost:3000/todos?userId=${userData.id}`,
            type: "GET",
            dataType: "json",
            success: function (todosData) {
              // Add todos to the user object
              userData.todos = todosData || [];
              console.log("User with todos:", userData);
              resolve(userData);
            },
            error: function (error) {
              console.error("Error fetching todos:", error);
              // Still resolve with user data even if todos fetch fails
              userData.todos = [];
              resolve(userData);
            },
          });
        } else {
          console.log("User not found!, creating new user...");
          postNewUser(username).then((userData) => {
            console.log("User created successfully:", userData);
            // New user has no todos yet
            userData.todos = [];
            resolve(userData);
          });
        }
      },
      error: function (error) {
        console.error("Error fetching user data:", error);
        reject(error);
      },
    });
  });
}

function todoCard(id, title, completed) {
  const statusText = completed ? "Completed" : "Pending";
  const borderColor = completed ? "border-emerald-500" : "border-amber-500";
  const bgColor = completed ? "bg-emerald-100" : "bg-amber-100";

  const $todoCard = $(
    `<div class="todo-card bg-opacity-10 ${bgColor} text-emerald-800 border ${borderColor} flex justify-between items-center px-4 py-2">
      <h3 class="font-medium cursor-pointer hover:${
        completed ? "text-emerald-600" : "text-amber-600"
      }" id="${id}">${title}</h3>
      <p class="text-sm"> <span class="${
        completed ? "text-emerald-600" : "text-amber-600"
      }">${statusText}</span></p>
    </div>`
  );
  return $todoCard;
}

function toggleTodo(userData) {
  const $todoListContainer = $("#todoListContainer");

  // Use event delegation to handle clicks on todo items
  $todoListContainer
    .off("click", ".todo-card h3")
    .on("click", ".todo-card h3", function (event) {
      event.preventDefault(); // Prevent default behavior

      const todoId = $(this).attr("id");
      const todoCompleted =
        $(this).closest(".todo-card").find("p span").text() === "Completed"
          ? false
          : true;

      const updatedTodo = {
        completed: todoCompleted,
      };

      $.ajax({
        url: `http://localhost:3000/todos/${todoId}`,
        type: "PATCH",
        dataType: "json",
        data: JSON.stringify(updatedTodo),
        contentType: "application/json",
        success: function (response) {
          console.log("Todo updated successfully:", response);

          // Update the local userData to reflect the change
          const todoIndex = userData.todos.findIndex(
            (todo) => todo.id == todoId
          );
          if (todoIndex !== -1) {
            userData.todos[todoIndex].completed = todoCompleted;
            localStorage.setItem("currentUser", JSON.stringify(userData));
          }

          loadTodoList(userData);
          toggleTodo(userData); // Re-attach event handlers after updating the todo list
        },
        error: function (error) {
          console.error("Error updating todo:", error);
        },
      });

      return false; // Prevent event bubbling
    });
}

function loadTodoList(userData) {
  const $todoListContainer = $("#todoListContainer");
  if (userData && userData.todos && userData.todos.length > 0) {
    $todoListContainer.empty(); // Clear existing todos
    userData.todos.forEach((todo) => {
      const todoCardElement = todoCard(todo.id, todo.title, todo.completed);
      $todoListContainer.append(todoCardElement);
    });
    toggleTodo(userData);
    console.log("Loaded todos:", userData.todos.length);
  } else {
    $todoListContainer.empty();
    $todoListContainer.append("<h3>Your task is empty</h3>");
    console.log("No todos found for this user.");
  }
}

function postNewTodos(userData) {
  $("#addTaskContainer form")
    .off("submit")
    .on("submit", function (event) {
      event.preventDefault();
      const $taskInput = $(this).find('input[name="task"]');
      const taskTitle = capitalizeFirst($taskInput.val().trim());

      if (taskTitle) {
        $.ajax({
          url: "http://localhost:3000/todos",
          type: "GET",
          dataType: "json",
          success: function (todos) {
            const nextId =
              todos.length > 0 ? Math.max(...todos.map((t) => t.id)) + 1 : 1;

            const newTodo = {
              id: toString(nextId),
              userId: userData.id, // Use userData instead of userStored
              title: taskTitle,
              completed: false,
            };

            $.ajax({
              url: "http://localhost:3000/todos",
              type: "POST",
              dataType: "json",
              data: JSON.stringify(newTodo),
              contentType: "application/json",
              success: function (response) {
                console.log("Todo created successfully:", response);

                // Update local userData
                userData.todos.push(response); // Use userData instead of userStored
                localStorage.setItem("currentUser", JSON.stringify(userData));

                $taskInput.val("");
                loadTodoList(userData);
              },
              error: function (error) {
                console.error("Error creating todo:", error);
              },
            });
          },
          error: function (error) {
            console.error("Error fetching todos:", error);
          },
        });
      }
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

      if ($("#todoListContainer").length === 0) {
        console.error("Todo list container not found in the DOM");
      } else {
        console.log("Loading todo list...");
        loadTodoList(userStored);
        postNewTodos(userStored);
      }
    } else {
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
