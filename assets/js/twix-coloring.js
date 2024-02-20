(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('bodybg') !== -1
    && document.documentElement.setAttribute('uitwix-coloring-body', 'on');
(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('asidebg') !== -1
    && document.documentElement.setAttribute('uitwix-coloring-sidebar', 'on');
(() => {
    const body_bgcolor = 'black'; // use from user defined settings
    const sidebar_bgcolor = '#403030'; // use from user defined settings
    const style = document.createElement('style');
    style.textContent = `
:root {
    --uitwix-body-bgcolor: ${body_bgcolor};
    --uitwix-sidebar-bgcolor: ${sidebar_bgcolor};
}`;
    document.head.appendChild(style);
})()