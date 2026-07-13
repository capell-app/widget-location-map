document.querySelectorAll('[data-capell-location-map]').forEach((root) =>
    root.querySelector('[data-map-activate]')?.addEventListener('click', () => {
        const status = root.querySelector('[data-map-status]')
        if (window.CapellPrivacy?.allows?.('openstreetmap') === false) {
            status.textContent =
                'Map loading is disabled by your privacy choices.'
            return
        }
        const latitude = Number(root.dataset.latitude)
        const longitude = Number(root.dataset.longitude)
        const delta = 0.02
        const frame = document.createElement('iframe')
        frame.title = 'Interactive location map'
        frame.loading = 'lazy'
        frame.referrerPolicy = 'strict-origin-when-cross-origin'
        frame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${longitude - delta}%2C${latitude - delta}%2C${longitude + delta}%2C${latitude + delta}&marker=${latitude}%2C${longitude}`
        root.querySelector('[data-map-frame]')?.replaceChildren(frame)
    }),
)
