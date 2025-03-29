


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


