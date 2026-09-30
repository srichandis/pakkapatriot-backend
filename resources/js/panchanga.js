/**
 * Temple Digital Panchangam — browser-side calculation.
 *
 * Ported from the React PanchangaPage. The astronomy comes from
 * @ishubhamx/panchangam-js (loaded from esm.sh, because the npm build pulls in
 * Node-only modules), the clock and every time is rendered in IST via Intl,
 * and the location comes from the browser with Bangalore as the fallback.
 */

const PANCHANGAM_MODULE = 'https://esm.sh/@ishubhamx/panchangam-js@2.2.6';

const DEFAULT_COORDS = { lat: 12.9716, lon: 77.5946 }; // Bangalore

const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const IST = 'Asia/Kolkata';

/** "HH:mm" in IST. */
const formatTime = (value) => {
    if (!value) return '--:--';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '--:--';

    return new Intl.DateTimeFormat('en-GB', {
        timeZone: IST,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(date);
};

/** The transition covering `now`, falling back to the first of the day. */
const current = (transitions, now) =>
    (transitions || []).find(
        (t) => new Date(t.startTime) <= now && new Date(t.endTime) >= now
    ) || (transitions || [])[0];

export default function initPanchanga() {
    const root = document.getElementById('panchangaRoot');
    if (!root) return;

    const fields = root.querySelectorAll('[data-panchanga-field]');
    const set = (field, value, sub) => {
        fields.forEach((node) => {
            if (node.dataset.panchangaField !== field) return;
            node.querySelector('[data-panchanga-value]').textContent = value || '—';
            const subNode = node.querySelector('[data-panchanga-sub]');
            if (subNode && sub) subNode.textContent = sub;
        });
    };

    const timeEl = root.querySelector('#panchangaTime');
    const dateEl = root.querySelector('#panchangaDate');
    const locationEl = root.querySelector('#panchangaLocation');
    const errorEl = root.querySelector('#panchangaError');

    const coords = { ...DEFAULT_COORDS };

    /* Clock — IST, ticking every second. */
    const tick = () => {
        const now = new Date();
        timeEl.textContent = new Intl.DateTimeFormat('en-GB', {
            timeZone: IST,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(now);
        dateEl.textContent = new Intl.DateTimeFormat('en-GB', {
            timeZone: IST,
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(now);
    };

    tick();
    setInterval(tick, 1000);

    const compute = async () => {
        try {
            // @vite-ignore keeps Vite from trying to bundle the remote module.
            const { getPanchangamDetails, Observer } = await import(/* @vite-ignore */ PANCHANGAM_MODULE);

            const observer = new Observer(coords.lat, coords.lon, 0);
            const now = new Date();
            const details = getPanchangamDetails(now, observer, { calendarType: 'amanta' });

            const tithi = current(details.tithiTransitions, now);
            const nakshatra = current(details.nakshatraTransitions, now);
            const yoga = current(details.yogaTransitions, now);
            const karana = current(details.karanaTransitions, now);

            set('samvatsara', details.samvat?.samvatsara ?? '—');
            set('ayana', details.ayana ?? '—');
            set('maasa', `${details.masa?.name ?? '—'}${details.masa?.isAdhika ? ' (Adhika)' : ''}`);
            set('paksha', details.paksha ?? '—');
            set('tithi', tithi?.name ?? '—', tithi?.endTime ? `Ends at ${formatTime(tithi.endTime)}` : '');
            set('vaara', DAYS[now.getDay()]);
            set('nakshatra', nakshatra?.name ?? '—', nakshatra?.endTime ? `Ends at ${formatTime(nakshatra.endTime)}` : '');
            set('yoga', yoga?.name ?? '—');
            set('karana', karana?.name ?? '—');
            set('sunrise', formatTime(details.sunrise));
            set('sunset', formatTime(details.sunset));

            errorEl.style.display = 'none';
        } catch (error) {
            console.error('Panchangam error:', error);
            errorEl.textContent = 'Could not load panchangam data. Retrying…';
            errorEl.style.display = 'flex';
        }
    };

    const locate = (position) => {
        coords.lat = position.coords.latitude;
        coords.lon = position.coords.longitude;
        locationEl.textContent = `${coords.lat.toFixed(2)}°, ${coords.lon.toFixed(2)}°`;
        compute();
    };

    /*
     * Only ask for a location when the host opts in. The full /panchangam page
     * sets data-panchanga-locate="auto"; the homepage's compact card does not,
     * so it renders for the default coordinates without a permission prompt.
     */
    if (root.dataset.panchangaLocate === 'auto' && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(locate, () => {
            locationEl.textContent = 'Bangalore (default)';
            compute();
        });
    } else {
        locationEl.textContent = 'Bangalore (default)';
        compute();
    }

    // Refresh the panchangam once a minute.
    setInterval(compute, 60_000);
}
