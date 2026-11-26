// Author: Ismaël

function loadCook() {
    const ticket = localStorage.getItem('ticket');
    apiPost('api/cook.php', { ticket: ticket }, function (result) {
        showCook(result.score, result.face);
    }, function (error) {
        console.error(error);
    });
}



function showCook(score, face) {
    const obj = document.getElementById('cook-score');
    obj.textContent = Math.round(score);
    drawFace(face);
}
