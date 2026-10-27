;(() => {
    const mounted = new WeakSet()
    const initialise = (root) => {
        if (mounted.has(root)) return
        mounted.add(root)
        root.querySelector('[data-map-activate]')?.addEventListener(
            'click',
            () => {
                const status = root.querySelector('[data-map-status]')
                if (window.CapellPrivacy?.allows?.('openstreetmap') === false) {
                    status.textContent =
                        root.dataset.deniedLabel ??
                        'Map loading is disabled by your privacy choices.'
                    return
                }
                status.textContent = ''
                const latitude = Number(root.dataset.latitude)
                const longitude = Number(root.dataset.longitude)
                const delta = 0.02
                const frame = document.createElement('iframe')
                frame.title =
                    root.dataset.frameTitle ?? 'Interactive location map'
                frame.loading = 'lazy'
                frame.referrerPolicy = 'strict-origin-when-cross-origin'
                frame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${longitude - delta}%2C${latitude - delta}%2C${longitude + delta}%2C${latitude + delta}&marker=${latitude}%2C${longitude}`
                root.querySelector('[data-map-frame]')?.replaceChildren(frame)
            },
        )
    }
    const mount = (root) => {
        if (root.matches?.('[data-capell-widget-location-map]'))
            initialise(root)
        root.querySelectorAll?.('[data-capell-widget-location-map]').forEach(
            initialise,
        )
    }
    mount(document)
    document.addEventListener('capell:content-ready', (event) =>
        mount(event.target),
    )
})()
