# Location Map Widget

<!-- prettier-ignore-start -->

Your branches appear on the page as a numbered list of up to twenty places. Each has its name, an address if you've given one, and a link that opens OpenStreetMap at that exact spot. The map is OpenStreetMap, not Google Maps.

Under the list sits a "Load interactive map" button. Nothing is fetched from OpenStreetMap until a visitor presses it, and the map that appears frames every place in your list, with a marker only when there is just one.

There's no postcode search. For each place you type in two numbers, its latitude and longitude, which you can copy from a map app.

If your site runs Privacy Centre and a visitor has turned down functional cookies in its cookie banner, pressing the button shows a short note and no map loads. The list and its links work for everyone either way.

## What it does

Location Map lets editors publish one to 20 validated coordinates for offices, venues, stores, and service points. Ordinary location links work before any map is activated.

## Do I need to do anything?

Add **Location Map** in Layout Builder, give it a title, then add one to twenty ordered markers. Each marker needs a label, latitude, and longitude; an address is optional. Coordinates must be within the valid latitude (`-90` to `90`) and longitude (`-180` to `180`) ranges. Preview the public page before publishing.

## Where it shows up

Editors configure it as a Layout Builder widget. The public fallback lists each marker with its optional address and a generated **View on OpenStreetMap** link. The interactive map enhances only after visitor activation and is centred on the first marker.

## Good to know

- The privacy-denied status and interactive frame title use the active page locale through the `denied` and `frame_title` translations.

- This free extension belongs to Capell Essentials and requires Layout Builder.
- The package does no geocoding: enter the coordinates you want to publish.
- It requests OpenStreetMap tiles only after explicit activation. When Privacy Centre denies `openstreetmap`, the visitor keeps the ordinary links and sees that map loading is disabled.

Widgets added through deferred content or inline reveal initialise when the content arrives. Repeated content-ready events do not duplicate listeners or countdown timers.

---

For developers: see the [README](../README.md).

<!-- prettier-ignore-end -->
