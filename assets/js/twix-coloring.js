const style = document.createElement('style');
style.textContent = `[uitwix-coloring] {
    --uitwix-body-bgcolor: black;
    --uitwix-sidebar-bgcolor: #403030;
}`;
document.head.appendChild(style);
document.documentElement.setAttribute('uitwix-coloring', 'on');