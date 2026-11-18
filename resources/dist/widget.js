;(() => {
    const mounted = new WeakSet()
    const embedUrl = (dataset) => {
        let bbox = String(dataset.bbox ?? '')
            .split(',')
            .map(Number)
        // Markup rendered before the map fitted every marker only carries one place.
        if (
            bbox.length !== 4 ||
            !bbox.every((value) => Number.isFinite(value))
        ) {
            const latitude = Number(dataset.latitude)
            const longitude = Number(dataset.longitude)
            const delta = 0.02
            bbox = [
                longitude - delta,
                latitude - delta,
                longitude + delta,
                latitude + delta,
            ]
        }
        const marker = String(
            dataset.marker ??
                (dataset.bbox
                    ? ''
                    : `${dataset.latitude},${dataset.longitude}`),
        )
            .split(',')
            .map(Number)
        const query = [`bbox=${bbox.join('%2C')}`]
        if (
            marker.length === 2 &&
            marker.every((value) => Number.isFinite(value))
        )
            query.push(`marker=${marker.join('%2C')}`)
        return `https://www.openstreetmap.org/export/embed.html?${query.join('&')}`
    }
    const initialise = (root) => {
        if (mounted.has(root)) return
        mounted.add(root)
        const activate = root.querySelector('[data-map-activate]')
        activate?.addEventListener('click', () => {
            const status = root.querySelector('[data-map-status]')
            if (window.CapellPrivacy?.allows?.('openstreetmap') === false) {
                status.textContent =
                    root.dataset.deniedLabel ??
                    'Map loading is disabled by your privacy choices.'
                return
            }
            status.textContent = ''
            const frame = document.createElement('iframe')
            frame.title = root.dataset.frameTitle ?? 'Interactive location map'
            frame.loading = 'lazy'
            frame.referrerPolicy = 'strict-origin-when-cross-origin'
            frame.src = embedUrl(root.dataset)
            root.querySelector('[data-map-frame]')?.replaceChildren(frame)
            // The button has done its job, and keyboard focus moves to the map it loaded.
            if (activate) activate.hidden = true
            frame.focus?.()
        })
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
