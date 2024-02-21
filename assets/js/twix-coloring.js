(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('bodybg') !== -1
    && document.documentElement.setAttribute('uitwix-coloring-body', 'on');
(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('asidebg') !== -1
    && document.documentElement.setAttribute('uitwix-coloring-sidebar', 'on');
(() => {
    const colors = Object.fromEntries(
        (`; ${document.cookie}`).split(`; uitwix-coloring=`).pop().split(';')[0].split('-').map(v => v.split(':'))
    );
    const style = document.createElement('style');
    style.textContent = `:root {
    --uitwix-body-bgcolor: ${colors.bodybg??'transparent'};
    --uitwix-sidebar-bgcolor: ${colors.asidebg??'transparent'};
}`;
    document.head.appendChild(style);
})()