import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import test from 'node:test'
import vm from 'node:vm'

const source = readFileSync(
    new URL('../../resources/dist/widget.js', import.meta.url),
    'utf8',
)

function activate(dataset) {
    let click
    let frame
    const button = {
        hidden: false,
        addEventListener: (event, listener) => {
            click = listener
        },
    }
    const root = {
        dataset,
        querySelector: (selector) =>
            ({
                '[data-map-activate]': button,
                '[data-map-status]': { textContent: '' },
                '[data-map-frame]': {
                    replaceChildren: (child) => {
                        frame = child
                    },
                },
            })[selector],
    }
    vm.runInNewContext(source, {
        document: {
            querySelectorAll: () => [root],
            addEventListener() {},
            createElement: () => ({
                focused: false,
                focus() {
                    this.focused = true
                },
            }),
        },
        window: {},
    })
    click()
    return { button, frame }
}

test('the embedded map uses the bounding box that fits every marker and no single marker', () => {
    const { frame } = activate({
        bbox: '-3.5,51.2,-1.5,56.2',
        latitude: '53.7',
        longitude: '-2.5',
    })
    assert.equal(
        frame.src,
        'https://www.openstreetmap.org/export/embed.html?bbox=-3.5%2C51.2%2C-1.5%2C56.2',
    )
})

test('a single place adds its marker', () => {
    const { frame } = activate({
        bbox: '-1.9,52.4,-1.8,52.5',
        marker: '52.4862,-1.8904',
    })
    assert.ok(frame.src.endsWith('&marker=52.4862%2C-1.8904'))
})

test('markup rendered before bounding boxes falls back to the single place it carries', () => {
    const { frame } = activate({ latitude: '52.5', longitude: '-1.5' })
    assert.ok(frame.src.includes('bbox=-1.52%2C52.48%2C-1.48%2C52.52'))
    assert.ok(frame.src.endsWith('&marker=52.5%2C-1.5'))
})

test('the activate button is hidden and focus moves to the loaded map', () => {
    const { button, frame } = activate({ bbox: '-1.9,52.4,-1.8,52.5' })
    assert.equal(button.hidden, true)
    assert.equal(frame.focused, true)
})
