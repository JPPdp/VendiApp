


// Calendar
const DAYS_TAG = document.querySelector(".DAYS"),
      CURRENT_DATE = document.querySelector(".CURRENT_DATE"),
      PREV_NEXT_ICON = document.querySelectorAll(".ICONS span");

let date = new Date(),
    currYear = date.getFullYear(),
    currMonth = date.getMonth();

const MONTHS = ["January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"];

const renderCalendar = () => {
    let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
        lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
        lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
        lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
    let liTag = "";
    for (let i = firstDayofMonth; i > 0; i--) {
        liTag += `<li class="INACTIVE">${lastDateofLastMonth - i + 1}</li>`;
    }
    for (let i = 1; i <= lastDateofMonth; i++) {
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() 
                     && currYear === new Date().getFullYear() ? "ACTIVE" : "";
        liTag += `<li class="${isToday}">${i}</li>`;
    }
    for (let i = lastDayofMonth; i < 6; i++) {
        liTag += `<li class="INACTIVE">${i - lastDayofMonth + 1}</li>`;
    }
    CURRENT_DATE.innerText = `${MONTHS[currMonth]} ${currYear}`;
    DAYS_TAG.innerHTML = liTag;
}

renderCalendar();

PREV_NEXT_ICON.forEach(icon => {
    icon.addEventListener("click", () => {
        currMonth = icon.id === "PREV" ? currMonth - 1 : currMonth + 1;
        if (currMonth < 0 || currMonth > 11) {
            date = new Date(currYear, currMonth, new Date().getDate());
            currYear = date.getFullYear();
            currMonth = date.getMonth();
        } else {
            date = new Date();
        }
        renderCalendar();
    });
});

// To-Do List
const TODO_BODY = document.getElementById("TODO_BODY");
const ADD_TASK = document.getElementById("ADD_TASK");

// Function to add a new task
function addTask(taskText) {
    if (taskText.length > 50) {
        alert("Task should be brief (max 50 characters).");
        return;
    }

    const newRow = document.createElement("tr");

    // Task Column
    const taskCell = document.createElement("td");
    taskCell.textContent = taskText;
    newRow.appendChild(taskCell);

    // Actions Column
    const actionsCell = document.createElement("td");
    actionsCell.classList.add("ACTIONS");

    const editIcon = document.createElement("i");
    editIcon.classList.add("fas", "fa-edit");
    editIcon.addEventListener("click", () => editTask(taskCell));

    const removeIcon = document.createElement("i");
    removeIcon.classList.add("fas", "fa-trash");
    removeIcon.addEventListener("click", () => removeTask(newRow));

    actionsCell.appendChild(editIcon);
    actionsCell.appendChild(removeIcon);
    newRow.appendChild(actionsCell);

    TODO_BODY.appendChild(newRow);
}

// Function to edit a task
function editTask(taskCell) {
    const currentText = taskCell.textContent;
    const newText = prompt("Edit your task:", currentText);
    if (newText !== null && newText.trim() !== "") {
        if (newText.length > 50) {
            alert("Task should be brief (max 50 characters).");
        } else {
            taskCell.textContent = newText;
        }
    }
}

// Function to remove a task
function removeTask(row) {
    row.remove();
}

// Add Task Button Click Event
ADD_TASK.addEventListener("click", () => {
    const taskText = prompt("Enter a new task:");
    if (taskText !== null && taskText.trim() !== "") {
        addTask(taskText);
    }
});
$(document).ready(function() {
    // Add Task Modal
    var addTaskModal = $('#addTaskModal');
    $('#ADD_TASK').on('click', function() {
        addTaskModal.show();
    });
    $('.close').on('click', function() {
        addTaskModal.hide();
        $('#editTaskModal').hide();
    });

    // Edit Task Modal
    var editTaskModal = $('#editTaskModal');
    $('.edit-btn').on('click', function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var task = row.find('.task').text();
        $('#editTaskId').val(id);
        $('#editTask').val(task);
        editTaskModal.show();
    });

    // Delete Task
    $('.delete-btn').on('click', function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        $.post('todo_action.php', { delete_task: true, id: id }, function(response) {
            location.reload();
        });
    });
});

