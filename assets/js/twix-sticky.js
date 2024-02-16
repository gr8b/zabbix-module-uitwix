document.addEventListener('DOMContentLoaded', e => {
    let stickies=[
        // Dashboard
        document.querySelector('.filter-space'), document.querySelector('.filter-space')?.previousElementSibling,
        // Other pages
        [...document.querySelectorAll('.filter-container')].filter(
            el => el.matches('.filter-container') && el.closest('.filter-space') === null
        )
    ];

    stickies.map(el => el instanceof HTMLElement && el.classList.add('uitwix-sticky'))
});