<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="/tododile/public/image/logo-tododile.png" />
    <link href="/tododile/public/css/output.css" rel="stylesheet" />
    <script src="/tododile/public/js/script.js"></script>
    <title>ToDoDile</title>
  </head>
  <body
    class="font-mono absolute inset-0 h-full w-full bg-white bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"
  >
    <!-- Header & Nav bar -->
    <header class="bg-white border-b-4 border-emerald-600">
      <nav
        class="mx-auto flex max-w-7xl items-center justify-between p-3 lg:px-8"
        aria-label="Global"
      >
        <div class="flex lg:flex-1">
          <a href="./" class="flex space-x-4 items-center m-1.5 p-1.5">
            <img
              class="h-8 lg:h-12 w-auto"
              src="/tododile/public/image/logo-tododile.png"
              alt="ToDoDile"
            />
            <div class="flex flex-col items-start spacing-y-0">
              <h1 class="text-emerald-600 font-bold text-md lg:text-xl">
                ToDoDile
              </h1>
              <p
                id="username"
                class="text-emerald-800 font-light text-xs lg:text-lg"
              >
                <?php echo isset($data['username']) ? 'Welcome, ' . htmlspecialchars($data['username']) . '!' : 'Welcome!'; ?>
              </p>
            </div>
          </a>
        </div>
        <div class="flex">
          <form method="post" action="/tododile/public/?url=auth/logout">
            <button
              class="bg-emerald-600 text-white hover:bg-emerald-700 px-6 py-2 cursor-pointer text-xs lg:text-base btn-fixed"
              id="logOutBtn"
              type="submit"
            >
              Logout
            </button>
          </form>
        </div>
      </nav>
    </header>
    <!-- Main -->
    <main
      class="mx-auto flex flex-col max-w-7xl items-center w-full space-y-4 p-4 lg:px-8"
      aria-label="Global"
    >
      <!-- Add Task -->
      <div
        id="addTaskContainer"
        class="w-full mx-auto flex items-center justify-between"
      >
        <form class="w-full flex items-center justify-between" method="post" action="/tododile/public/?url=home/addTask">
          <div class="w-full flex items-center space-x-4">
            <input
              type="text"
              name="task"
              class="flex-grow px-4 py-2 border-2 border-emerald-500 focus:outline-none focus:border-emerald-600 text-xs lg:text-base"
              placeholder="Add a new task"
              required
              aria-label="Add a new task"
            />
            <button
              type="submit"
              class="bg-emerald-600 text-white py-2 px-4 hover:bg-emerald-700 transition cursor-pointer text-xs lg:text-base btn-fixed"
            >
              Add Task
            </button>
          </div>
        </form>
      </div>
      <div class="border-2 border-emerald-500 mx-auto w-full p-4">
        <ul class="space-y-2">
          <?php if (!empty($data['tasks'])): ?>
            <?php foreach ($data['tasks'] as $task): ?>
              <li class="flex items-center justify-between py-2">
                <form method="post" action="/tododile/public/?url=home/updateTask/<?php echo $task['id']; ?>" class="flex-grow flex items-center space-x-2">
                  <input 
                    type="text" 
                    name="task" 
                    value="<?php echo htmlspecialchars($task['task']); ?>" 
                    class="task-text flex-grow px-2 py-1 border cursor-pointer <?php echo isset($task['completed']) && $task['completed'] ? 'bg-emerald-100 text-emerald-600 bg-opacity-30' : 'text-amber-600 bg-amber-100 bg-opacity-30'; ?>"
                    readonly="readonly"
                    data-original="<?php echo htmlspecialchars($task['task']); ?>"
                  />
                  <button type="button" class="edit-btn text-xs bg-emerald-500 text-white px-2 py-1 hover:bg-emerald-700 btn-fixed">Edit</button>
                </form>
                
                <!-- Toggle completion status form -->
                <form method="post" action="/tododile/public/?url=home/toggleStatus/<?php echo $task['id']; ?>" class="toggle-form hidden">
                  <input type="hidden" name="completed" value="<?php echo isset($task['completed']) && $task['completed'] ? '0' : '1'; ?>">
                </form>
                
                <!-- Delete form -->
                <form method="post" action="/tododile/public/?url=home/deleteTask/<?php echo $task['id']; ?>">
                  <button type="submit" class="delete-btn text-xs bg-red-500 text-white px-2 py-1 hover:bg-red-700 ml-2 btn-fixed" data-action="delete">Delete</button>
                </form>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="text-gray-500">No tasks found.</li>
          <?php endif; ?>
        </ul>
      </div>
    </main>
  </body>
</html>
