import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { runInNewContext } from 'node:vm'
const dataset = JSON.parse(readFileSync(0, 'utf8'))
let activate
let frame
let allowed = false
const status = { textContent: '' }
const root = {
    dataset,
    querySelector: (selector) =>
        ({
            '[data-map-activate]': {
                addEventListener: (event, listener) => {
                    assert.equal(event, 'click')
                    activate = listener
                },
            },
            '[data-map-status]': status,
            '[data-map-frame]': {
                replaceChildren: (child) => {
                    frame = child
                },
            },
        })[selector],
}
runInNewContext(readFileSync(process.argv[2], 'utf8'), {
    document: {
        querySelectorAll: () => [root],
        addEventListener: () => {},
        createElement: (tag) => {
            assert.equal(tag, 'iframe')
            return {}
        },
    },
    window: {
        CapellPrivacy: {
            allows: (provider) => {
                assert.equal(provider, 'openstreetmap')
                return allowed
            },
        },
    },
})
activate()
assert.equal(status.textContent, 'Carte refusée <test>')
assert.equal(frame, undefined)
allowed = true
activate()
assert.equal(frame.title, 'Carte interactive')
assert.match(frame.src, /^https:\/\/www.openstreetmap.org\/export\/embed.html/)
