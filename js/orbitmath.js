// Author: Ismaël

let bands = null;

function loadBands() {
    return fetch('shared/bands.json')
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            bands = data;
        });
}

function isFar(deadline, now, scale) {
    const date = new Date(deadline.replace(' ', 'T'));
    const hoursleft = (date - now) / 3600000;
    return hoursleft > scale.rangeHours;
}

function radiusFor(deadline, now, scale) {
    if (isFar(deadline, now, scale)) {
        return 450;
    }
    const date = new Date(deadline.replace(' ', 'T'));
    const hoursleft = (date - now) / 3600000;
    const f = Math.max(hoursleft / scale.rangeHours, 0);
    return f * 400;
}

function angleFor(id) {
    return (id * 2.4) % (2 * Math.PI);
}

function sizeFor(weight, deadline) {
    const date = new Date(deadline.replace(' ', 'T'));
    const hoursleft = (date - new Date()) / 3600000;
    const scale = bands.scales.find(function (s) {
        return hoursleft <= s.rangeHours;
    });
    const coef = scale ? scale.coef : bands.farCoef;
    return 8 + weight * coef * 0.6;
}

function polarToXY(r, angle) {
    return { x: r * Math.cos(angle), y: r * Math.sin(angle) };
}
