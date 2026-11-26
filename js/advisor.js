// Author: Ismaël

function askAdvisor() {
    document.getElementById('advisor-button').disabled = true;
    document.getElementById('advisor-text').textContent = 'Thinking...';
    const ticket = localStorage.getItem('ticket');
    apiPost('api/advisor.php', { ticket: ticket }, function (result) {
        showAdvice(result.advice);
    }, function () {
        showAdviceError();
    });
}

function showAdvice(text) {
    document.getElementById('advisor-text').textContent = text;
    document.getElementById('advisor-button').disabled = false;
}

function showAdviceError() {
    document.getElementById('advisor-text').textContent = "oops, looks like I'm cooked just like you ! try again in a minute !";
    document.getElementById('advisor-button').disabled = false;
}
