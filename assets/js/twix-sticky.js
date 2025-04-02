(`; ${document.cookie}`).split(`; uitwix=`).pop().split(';')[0].split('-').indexOf('sticky') !== -1
&& document.addEventListener('DOMContentLoaded', e =>
    [
        // Dashboard
        document.querySelector('.filter-space'), document.querySelector('.filter-space')?.previousElementSibling,
        // Latest data
        document.querySelector('#monitoring_latest_filter'),
        // Other pages
        [...document.querySelectorAll('.filter-container')].filter(
            el => el.matches('.filter-container') && el.closest('.filter-space') === null
        )
    ].map(el => el instanceof HTMLElement && el.classList.add('uitwix-sticky'))
    && document.addEventListener('click',
        e => e.target.closest('.tabfilter-subfilter')?.classList.toggle('uitwix-subfilter-collapsed')
    )
);
