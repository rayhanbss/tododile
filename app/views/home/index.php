<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="/tododile/public/image/logo-tododile.png" />
    <link href="/tododile/public/css/output.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,200,0,0" />
    <script src="/tododile/public/js/script.js"></script>
    <title>ToDoDile</title>
  </head>
  <body
    class="font-mono inset-0 h-full w-full bg-white bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"
  >
    <?php if (isset($_SESSION['flash_message'])): ?>
        <?php $flash = $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
        <div id="flashMessage" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[9999] flex flex-row items-center justify-between max-w-md w-auto mx-4 border px-6 py-3 space-x-2 shadow-2xl transition-all duration-300 opacity-100
            <?php echo $flash['type'] === 'success' ? 'bg-emerald-100 text-emerald-600 border-emerald-600 bg-opacity-90' : 'bg-red-100 text-red-600 border-red-600 bg-opacity-90'; ?>"
            style="pointer-events:auto;">
              <div class="mr-3">
                <?php echo $flash['message']; ?>
              </div>
              <button type="button" onclick="hideToast()" class="flex items-center justify-center w-6 h-6 cursor-pointer transition-colors">
                <span class="material-symbols-outlined text-sm hover:text-md">close</span>
              </button>
        </div>
    <?php endif; ?>

    <!-- Header & Nav bar -->
    <header class="bg-white border-b-4 border-emerald-600">
      <nav
        class="mx-auto flex max-w-7xl items-center justify-between p-3 lg:px-8"
        aria-label="Global"
      >
        <div class="flex lg:flex-1">
          <a href="./home" class="flex space-x-4 items-center p-1.5">
            <img
              class="h-6 lg:h-8 w-auto"
              src="/tododile/public/image/logo-tododile.png"
              alt="ToDoDile"
            />
            <h1 class="text-emerald-600 font-bold text-md lg:text-xl">
              ToDoDile
            </h1>
          </a>
        </div>
        <div class="flex flex-row items-center border bg-emerald-100 bg-opacity-30 border-emerald-500">
          <p id="username" class="flex  text-emerald-600 font-light px-4 text-sm lg:text-md">
            <?php echo isset($data['username']) ? 'Welcome, ' . htmlspecialchars($data['username']) . '!' : 'Welcome!'; ?>
          </p>
          <form method="post" action="/tododile/public/login/logout"">
            <button
              class="flex bg-emerald-500 text-white hover:bg-emerald-700 px-2 cursor-pointer text-xs btn-fixed"
              id="logOutBtn"
              type="submit"
            >
              <span class="edit-btn material-symbols-outlined py-1">logout</span>
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
        <form class="w-full flex items-center justify-between" method="post" action="/tododile/public/home/addTask">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <div class="w-full flex items-center border-2 border-emerald-500">
            <input
              type="text"
              name="task"
              class="flex-grow py-2 px-4 focus:outline-none text-xs lg:text-base"
              placeholder="Add a new task"
              required
              aria-label="Add a new task"
            />
            <button
              type="submit"
              class="bg-emerald-500 text-white py-2 px-4 hover:bg-emerald-700 transition cursor-pointer text-xs lg:text-base btn-fixed"
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
              <li class="flex items-center justify-between h-10 py-2 space-x-2">
                <form method="post" action="/tododile/public/home/updateTask/<?php echo $task['id']; ?>" class="flex-grow flex items-center space-x-2">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <input 
                    type="text" 
                    name="task" 
                    value="<?php echo htmlspecialchars($task['task']); ?>" 
                    class="task-text flex-grow w-16 px-2 py-1 border cursor-pointer <?php echo isset($task['completed']) && $task['completed'] ? 'bg-emerald-100 text-emerald-600 bg-opacity-30' : 'text-amber-600 bg-amber-100 bg-opacity-30'; ?>"
                    readonly="readonly"
                    data-original="<?php echo htmlspecialchars($task['task']); ?>"
                  />
                  <button type="button" class="flex items-center h-8 px-4 border text-emerald-600 bg-emerald-100 bg-opacity-30 border-emerald-600 hover:bg-emerald-400 cursor-pointer btn-fixed">
                    <span class="edit-btn material-symbols-outlined text-xs p-2">edit</span>
                  </button>
                </form>
                
                <!-- Toggle completion status form -->
                <form method="post" action="/tododile/public/home/toggleStatus/<?php echo $task['id']; ?>" class="toggle-form hidden">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <input type="hidden" name="completed" value="<?php echo isset($task['completed']) && $task['completed'] ? '0' : '1'; ?>">
                </form>
                
                <!-- Delete form -->
                <form method="post" action="/tododile/public/home/deleteTask/<?php echo $task['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <button type="submit" class="flex items-center h-8 px-4 border text-red-600 bg-red-100 bg-opacity-30 border-red-600 hover:bg-red-300 cursor-pointer btn-fixed">
                      <span class="delete-btn material-symbols-outlined text-xs p-2">delete</span>
                    </button>
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
