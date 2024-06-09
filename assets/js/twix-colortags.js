(() => {
    const style = document.createElement('style');
    let colortags = decodeURIComponent((`; ${document.cookie}`).split(`; uitwix-colortags=`).pop().split(';')[0])
        .split("\n");

    for (let i = 0; i < colortags.length; i += 3) {
        let value = colortags[i].replaceAll('"', '\\"');
        let match = parseInt(colortags[i + 1], 10);
        let color = colortags[i + 2];

        switch (match) {
            case 1:
                style.textContent += `.tag[data-hintbox-contents^="${value}"] { background-color: ${color} }`;
                break;

            case 2:
                style.textContent += `.tag[data-hintbox-contents*="${value}"] { background-color: ${color} }`;
                break;

            case 3:
                style.textContent += `.tag[data-hintbox-contents$="${value}"] { background-color: ${color} }`;
                break;
        }
    }

    document.head.appendChild(style);
})()