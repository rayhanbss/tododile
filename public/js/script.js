document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const taskItem = this.closest("li");
      const taskInput = taskItem.querySelector('input[name="task"]');
      const deleteBtn = taskItem.querySelector(".delete-btn");
      const taskForm = taskItem.querySelector("form");

      // Toggle edit mode
      if (this.textContent.trim() === "Edit") {
        // Enter edit mode
        this.textContent = "Save";
        taskInput.removeAttribute("readonly");
        // Add Tailwind classes to override focus styles
        taskInput.classList.add(
          "focus:outline-none",
          "focus:border-emerald-500",
          "focus:border-2",
          "focus:rounded-none"
        );
        taskInput.focus();
        deleteBtn.textContent = "Discard";
        deleteBtn.dataset.action = "discard";
      } else {
        // Save changes
        this.textContent = "Edit";
        taskInput.setAttribute("readonly", "readonly");
        // Remove the focus override classes
        taskInput.classList.remove(
          "focus:outline-none",
          "focus:border-emerald-500",
          "focus:border-2",
          "focus:rounded-none"
        );
        deleteBtn.textContent = "Delete";
        deleteBtn.dataset.action = "delete";

        if (taskInput.value.trim() !== "") {
          taskForm.submit();
        }
      }
    });
  });

  // Handle delete/discard button
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      const taskItem = this.closest("li");
      const taskInput = taskItem.querySelector('input[name="task"]');
      const editBtn = taskItem.querySelector(".edit-btn");

      if (this.dataset.action === "discard") {
        e.preventDefault(); // Prevent form submission
        // Discard changes
        taskInput.value = taskInput.dataset.original;
        taskInput.setAttribute("readonly", "readonly");
        editBtn.textContent = "Edit";
        editBtn.classList.remove("bg-blue-500");
        editBtn.classList.add("bg-emerald-500");
        this.textContent = "Delete";
        this.dataset.action = "delete";
      }
      // If action is "delete", let the form submit normally
    });
  });

  // Handle task text clicks for toggling completion
  document.querySelectorAll(".task-text").forEach((input) => {
    input.addEventListener("click", function (event) {
      // Only toggle if the input is readonly (not in edit mode)
      if (this.readOnly) {
        event.preventDefault();
        // Find and submit the toggle form
        const toggleForm = this.closest("li").querySelector(".toggle-form");
        if (toggleForm) {
          toggleForm.submit();
        }
      }
    });

    // Prevent focus styling when clicking
    input.addEventListener("mousedown", function (event) {
      if (this.readOnly) {
        event.preventDefault();
      }
    });
  });
});
