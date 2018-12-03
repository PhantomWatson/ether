function toggleBGFade(fade_interval) {
    if (! fade_interval) {
        fade_interval = 1000;
    }
    if (fading_interval_id) {
        clearInterval(fading_interval_id);
        fading_interval_id = 0;
    } else {
        fading_interval_id = setInterval(function() {adjustBackground();}, fade_interval);
    }
}

function adjustBackground() {
    if (fading_mouseover_pause) {
        return;
    }
    var upper_limit = 255;
    var lower_limit = 0;
    var body_tag = document.getElementById('body_tag');
    var color = new RGBColor(body_tag.style.backgroundColor);
    var rand_color = Math.floor(Math.random()*3);
    var adjustment = Math.round(Math.random()) === 0 ? -1 : 1;
    if (rand_color === 0) {
        target_color = color.r;
    } else if (rand_color === 1) {
        target_color = color.g;
    } else if (rand_color === 2) {
        target_color = color.b;
    }
    if (adjustment === 1 && target_color >= upper_limit) {
        adjustment = -1;
    } else if (adjustment === -1 && target_color <= lower_limit) {
        adjustment = 1;
    }
    if (rand_color === 0) {
        color.r += adjustment;
    } else if (rand_color === 1) {
        color.g += adjustment;
    } else if (rand_color === 2) {
        color.b += adjustment;
    }
    document.getElementById('toggleBGFade').innerHTML = color.toHex();
    document.getElementById('toggleBGFade').style.color = color.toHex();
    body_tag.style.backgroundColor = color.toHex();
}
