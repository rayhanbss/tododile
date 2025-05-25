<!DOCTYPE html>
<html lang="en">  <head>
    <meta charset="UTF-8" />    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="/tododile/public/image/logo-tododile.png" />
    <link href="/tododile/public/css/output.css" rel="stylesheet" />
    <title>Welcome to ToDoDile</title>
  </head>
  <body
    class="font-mono absolute inset-0 h-full w-full bg-white bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"
  >
    <main
      class="mx-auto flex flex-row justify-center max-w-7xl items-center w-full h-full px-4 sm:px-6 lg:px-8"
    >
      <div
        class="bg-white border-4 border-emerald-600 shadow-xl p-3 sm:p-6 w-full max-w-md mx-auto space-y-4"
      >
        <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">
          Welcome to ToDoDile!
        </h2>
        <?php if (!empty($data['error'])): ?>
          <div class="text-red-600 text-sm mb-2"><?php echo $data['error']; ?></div>
        <?php endif; ?>        
        <form method="post" action="/tododile/register">
          <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-medium mb-1"
              >Type in your username</label
            >
            <input
              type="text"
              name="username"
              class="w-full px-3 sm:px-4 py-2 border-2 border-emerald-500 focus:outline-none focus:border-emerald-600 text-sm sm:text-base"
              placeholder="username"
              value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
              required
            />
          </div>
          <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-medium mb-1"
              >Password</label
            >
            <input
              type="password"
              name="password"
              class="w-full px-3 sm:px-4 py-2 border-2 border-emerald-500 focus:outline-none focus:border-emerald-600 text-sm sm:text-base"
              placeholder="password"
              required
            />
          </div>
          <button
            type="submit"
            class="w-full bg-emerald-600 text-white py-2 hover:bg-emerald-700 transition text-sm sm:text-base"
          >
            Join ToDoDile!
          </button>
        </form>        <div class="flex justify-center">
            <p class="text-xs sm:text-sm text-gray-500 mt-4">
            Already have an account? <a href="/tododile/public/login" class="text-emerald-600 hover:underline">Login here</a>.
            </p>
        </div>
      </div>
    </main>
  </body>
</html>