// Author: Ismaël

const shownIds = new Set();

function startCenterWatch() {
    function check() {
        const ticket = localStorage.getItem('ticket');
        apiPost('api/events.php', { ticket: ticket }, function (events) {
            findDue(events, new Date()).forEach(function (event) {
                if (shownIds.has(event.id)) {
                    return;
                }
                shownIds.add(event.id);
                if (event.type === 'happening') {
                    showReminder(event);
                } else {
                    showTaskAlert(event);
                }
            });
        }, function (error) {
            console.error(error);
        });
    }

    check();
    setInterval(check, 30000);
}

function findDue(events, now) {
    return events.filter(function (event) {
        const deadline = new Date(event.deadline.replace(' ', 'T'));
        return deadline <= now;
    });
}

function showReminder(event) {
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.textContent = "It's today: " + event.name;

    const button = document.createElement('button');
    button.textContent = 'OK';
    button.addEventListener('click', function () {
        popup.remove();
    });

    popup.appendChild(button);
    document.body.appendChild(popup);
}

function showTaskAlert(event) {
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.textContent = "Time's up: " + event.name;

    const done = document.createElement('button');
    done.textContent = 'Done!';
    done.addEventListener('click', function () {
        sendResolve(event.id, 'done');
        popup.remove();
    });

    const drop = document.createElement('button');
    drop.textContent = 'Drop it?';
    drop.addEventListener('click', function () {
        sendResolve(event.id, 'drop');
        popup.remove();
    });

    const dateInput = document.createElement('input');
    dateInput.type = 'date';

    const postpone = document.createElement('button');
    postpone.textContent = 'Postpone it?';
    postpone.addEventListener('click', function () {
        if (!dateInput.value) {
            dateInput.focus();
            return;
        }
        sendResolve(event.id, 'postpone', dateInput.value);
        popup.remove();
    });

    popup.append(done, drop, dateInput, postpone);
    document.body.appendChild(popup);
}

function sendResolve(id, action, newDeadline) {
    const ticket = localStorage.getItem('ticket');
    const data = { ticket: ticket, id: id, action: action };
    if (newDeadline) {
        data.newDeadline = newDeadline;
    }

    apiPost('api/resolve.php', data, function () {
        shownIds.delete(id);
        loadCook();
        apiPost('api/events.php', { ticket: ticket }, drawOrbit, function (error) {
            console.error(error);
        });
    }, function (error) {
        shownIds.delete(id);
        console.error(error);
    });
}
