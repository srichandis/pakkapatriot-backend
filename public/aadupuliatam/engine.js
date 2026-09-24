// ===== RULES-ENGINE-START =====
/* Pure game rules for Aadu Puli Aatam (ஆடு புலி ஆட்டம்) — Goats & Tigers.
   The classic South-hunting game of Bhārat: 3 tigers hunt 15 goats on a
   23-point triangular board. No DOM dependencies — testable in Node. */
const AaduPuliRules = (function () {
  'use strict';

  const TIGER_COUNT = 3;
  const GOAT_COUNT = 15;
  const CAPTURES_TO_WIN = 5;
  const DRAW_ROUNDS = 30;

  /* The tigers start on the innermost apex triangle: the apex and the two
     points directly below it. */
  const TIGER_START = [0, 3, 4];

  /* Board rows, top → bottom. Classic layout: apex (1) + rows 1-3 of six
     positions each + a bottom row of four = 23 points. */
  const ROWS = [
    [0],
    [1, 2, 3, 4, 5, 6],
    [7, 8, 9, 10, 11, 12],
    [13, 14, 15, 16, 17, 18],
    [19, 20, 21, 22]
  ];

  /* Point coordinates in a 100×100 space. The four "fan" chains running from
     the apex down to the bottom row are exactly collinear, so jump-captures
     along them are geometrically exact. */
  const PTS = {
    0: [50, 6],
    1: [10, 26], 2: [43, 26], 3: [47, 26], 4: [53, 26], 5: [57, 26], 6: [90, 26],
    7: [10, 46], 8: [36, 46], 9: [44, 46], 10: [56, 46], 11: [64, 46], 12: [90, 46],
    13: [10, 66], 14: [29, 66], 15: [41, 66], 16: [59, 66], 17: [71, 66], 18: [90, 66],
    19: [22, 86], 20: [38, 86], 21: [62, 86], 22: [78, 86]
  };

  /* The drawn lines of the board: horizontal rows, the two vertical edges and
     the four apex fans. Movement and captures happen only along these lines. */
  const FANS = [
    [0, 2, 8, 14, 19],
    [0, 3, 9, 15, 20],
    [0, 4, 10, 16, 21],
    [0, 5, 11, 17, 22]
  ];

  function buildGeometry() {
    const N = 23;
    const adj = Array.from({ length: N }, () => []);
    const link = (a, b) => { if (a !== b && adj[a].indexOf(b) === -1) { adj[a].push(b); adj[b].push(a); } };

    // horizontal neighbours within each row
    for (const row of ROWS) for (let i = 0; i < row.length - 1; i++) link(row[i], row[i + 1]);
    // apex fans
    for (const f of FANS) for (let i = 0; i < f.length - 1; i++) link(f[i], f[i + 1]);
    // vertical edge columns
    link(1, 7); link(7, 13);
    link(6, 12); link(12, 18);

    // capture lines (every straight, connected chain of the board)
    const lines = ROWS.map(r => r.slice()).concat([[1, 7, 13], [6, 12, 18]]).concat(FANS.map(f => f.slice()));

    // For a tiger at p: every {goat, land} pair such that p-goat-land are
    // three consecutive points on one straight line.
    const capTable = Array.from({ length: N }, () => []);
    for (const line of lines) {
      for (let i = 0; i < line.length; i++) {
        const p = line[i];
        if (i + 2 < line.length) capTable[p].push({ goat: line[i + 1], land: line[i + 2] });
        if (i - 2 >= 0) capTable[p].push({ goat: line[i - 1], land: line[i - 2] });
      }
    }

    return { N, ROWS, PTS, adj, lines, capTable, fans: FANS, TIGER_START, GOAT_COUNT };
  }

  /* ---------------- state ---------------- */
  function createGame({ names, opts }) {
    const g = buildGeometry();
    return Object.assign({}, g, {
      names: names || ['Goats', 'Tigers'],
      opts: Object.assign({}, opts),
      tigers: TIGER_START.slice(),
      goats: new Set(),
      goatsPlaced: 0,
      phase: 'place',      // goats are placing (or, later, moving)
      turn: 'goat',        // 'goat' | 'tiger'
      captures: 0,
      over: false,
      winner: null,        // 'goats' | 'tigers' | 'draw'
      rounds: 0            // full goat→tiger rounds completed
    });
  }

  function emptyPoints(state) {
    const res = [];
    for (let p = 0; p < state.N; p++) {
      if (!state.goats.has(p) && state.tigers.indexOf(p) === -1) res.push(p);
    }
    return res;
  }

  /* ---------------- legal moves ---------------- */
  function getGoatMoves(state) {
    if (state.phase === 'place') {
      return emptyPoints(state).map(p => ({ type: 'place', to: p }));
    }
    const moves = [];
    state.goats.forEach(g => {
      for (const n of state.adj[g]) {
        if (!state.goats.has(n) && state.tigers.indexOf(n) === -1) moves.push({ type: 'move', goat: g, to: n });
      }
    });
    return moves;
  }

  /* Which tigers could capture a goat sitting at pt right now
     (tiger adjacent to pt, landing point empty). */
  function capturableBy(state, pt) {
    const out = [];
    for (let t = 0; t < state.tigers.length; t++) {
      const tp = state.tigers[t];
      for (const c of state.capTable[tp]) {
        if (c.goat === pt && !state.goats.has(c.land) && state.tigers.indexOf(c.land) === -1) {
          out.push({ tiger: t, land: c.land });
        }
      }
    }
    return out;
  }

  function getTigerMoves(state) {
    const moves = [];
    for (let t = 0; t < state.tigers.length; t++) {
      const tp = state.tigers[t];
      for (const c of state.capTable[tp]) {
        if (state.goats.has(c.goat) && !state.goats.has(c.land) && state.tigers.indexOf(c.land) === -1) {
          moves.push({ type: 'capture', tiger: t, from: tp, goat: c.goat, to: c.land });
        }
      }
      for (const n of state.adj[tp]) {
        if (!state.goats.has(n) && state.tigers.indexOf(n) === -1) {
          moves.push({ type: 'move', tiger: t, from: tp, to: n });
        }
      }
    }
    return moves;
  }

  function tigersBlocked(state) { return getTigerMoves(state).length === 0; }

  /* ---------------- applying moves ---------------- */
  function applyGoatMove(state, move) {
    const events = [{ type: move.type, to: move.to }];
    if (move.type === 'place') {
      state.goats.add(move.to);
      state.goatsPlaced++;
      if (state.goatsPlaced >= GOAT_COUNT) {
        state.phase = 'move';
        events.push({ type: 'phase', phase: 'move' });
      }
    } else {
      state.goats.delete(move.goat);
      state.goats.add(move.to);
      events[0].from = move.goat;
    }
    return events;
  }

  function applyTigerMove(state, move) {
    const events = [{ type: move.type, tiger: move.tiger, from: move.from, to: move.to }];
    if (move.type === 'capture') {
      state.goats.delete(move.goat);
      state.captures++;
      events[0].goat = move.goat;
      events[0].total = state.captures;
      if (state.captures >= CAPTURES_TO_WIN) { state.over = true; state.winner = 'tigers'; }
    }
    state.tigers[move.tiger] = move.to;
    return events;
  }

  /* If the goat just placed at pt is sitting in a tiger's jaws, return the
     forced immediate capture; otherwise null. */
  function immediateCapture(state, pt) {
    const c = capturableBy(state, pt);
    if (!c.length) return null;
    return { type: 'capture', tiger: c[0].tiger, from: state.tigers[c[0].tiger], goat: pt, to: c[0].land };
  }

  /* Goats win the moment none of the tigers can move. */
  function checkGoatsWin(state) {
    if (tigersBlocked(state)) { state.over = true; state.winner = 'goats'; return true; }
    return false;
  }

  /* ---------------- AI ---------------- */
  function aiChooseGoatPlacement(state) {
    const empties = emptyPoints(state);
    // landing points that would block a tiger's imminent capture if a goat sits there
    const blockPoints = new Set();
    for (const tp of state.tigers) {
      for (const c of state.capTable[tp]) {
        if (state.goats.has(c.goat) && !state.goats.has(c.land) && state.tigers.indexOf(c.land) === -1) {
          blockPoints.add(c.land);
        }
      }
    }
    let best = null, bestScore = -Infinity;
    for (const p of empties) {
      let s = Math.random() * 4;
      if (capturableBy(state, p).length > 0) s -= 100000;      // never feed a tiger
      if (blockPoints.has(p)) s += 200;                        // BLOCK an imminent tiger capture
      let adjT = 0;
      for (const n of state.adj[p]) if (state.tigers.indexOf(n) !== -1) adjT++;
      s += adjT * 8;                                            // blocking power
      s += state.adj[p].length * 2;                             // high-degree points matter
      if (p === 0) s += 16;                                     // take the apex when free
      else if (p <= 6) s += 7;                                  // row 1
      else if (p <= 12) s += 4;                                 // row 2
      if (s > bestScore) { bestScore = s; best = { type: 'place', to: p }; }
    }
    return best;
  }

  function aiChooseGoatMove(state) {
    const moves = getGoatMoves(state);
    if (!moves.length) return null;
    // landing points that would block a tiger's imminent capture if a goat sits there
    const blockPoints = new Set();
    for (const tp of state.tigers) {
      for (const c of state.capTable[tp]) {
        if (state.goats.has(c.goat) && !state.goats.has(c.land) && state.tigers.indexOf(c.land) === -1) {
          blockPoints.add(c.land);
        }
      }
    }
    let best = null, bestScore = -Infinity;
    for (const m of moves) {
      let s = Math.random() * 4;
      if (capturableBy(state, m.to).length > 0) s -= 100000;    // don't step into a trap
      if (blockPoints.has(m.to)) s += 200;                      // move onto a landing point to BLOCK
      let adjT = 0, fromT = 0;
      for (const n of state.adj[m.to]) if (state.tigers.indexOf(n) !== -1) adjT++;
      for (const n of state.adj[m.goat]) if (state.tigers.indexOf(n) !== -1) fromT++;
      s += adjT * 6;
      s += state.adj[m.to].length * 1.5;
      s -= fromT * 4;                                           // don't abandon a blockade needlessly
      if (s > bestScore) { bestScore = s; best = m; }
    }
    return best;
  }

  function aiChooseTigerMove(state) {
    const moves = getTigerMoves(state);
    if (!moves.length) return null;
    const pool = moves.some(m => m.type === 'capture') ? moves.filter(m => m.type === 'capture') : moves;
    let best = null, bestScore = -Infinity;
    for (const m of pool) {
      let s = Math.random() * 3;
      if (m.type === 'capture') s += 1000;
      let threat = 0, adjG = 0;
      for (const c of state.capTable[m.to]) {
        if (state.goats.has(c.goat) && !state.goats.has(c.land) && state.tigers.indexOf(c.land) === -1) threat += 5;
      }
      for (const n of state.adj[m.to]) if (state.goats.has(n)) adjG++;
      s += adjG * 2 + threat + state.adj[m.to].length;          // stay central & dangerous
      if (s > bestScore) { bestScore = s; best = m; }
    }
    return best;
  }

  /* ---------------- self tests ---------------- */
  function runSelfTests() {
    const results = [];
    const ok = (name, cond) => results.push({ name, pass: !!cond });
    const eq = (name, a, b) => results.push({ name, pass: JSON.stringify(a) === JSON.stringify(b) });

    const g = buildGeometry();
    ok('23 points', g.N === 23);
    ok('rows sum to 23', g.ROWS.reduce((s, r) => s + r.length, 0) === 23);
    eq('rows structure', g.ROWS.map(r => r.length).join(','), '1,6,6,6,4');

    // graph sanity
    ok('apex connects to 4 inner row-1 points', g.adj[0].sort((a, b) => a - b).join(',') === '2,3,4,5');
    ok('row1 horizontal chain', g.adj[3].indexOf(2) !== -1 && g.adj[3].indexOf(4) !== -1 && g.adj[3].indexOf(9) !== -1);
    ok('edge column vertical', g.adj[1].indexOf(7) !== -1 && g.adj[7].indexOf(13) !== -1);
    ok('all points connected (no degree 0)', g.adj.every(a => a.length > 0));
    // connectedness: BFS from 0 reaches everything
    const seen = new Set([0]); const q = [0];
    while (q.length) { const p = q.pop(); for (const n of g.adj[p]) if (!seen.has(n)) { seen.add(n); q.push(n); } }
    ok('board fully connected', seen.size === 23);

    // collinearity of fans is exact
    function cross(a, b, c) { return (b[0] - a[0]) * (c[1] - a[1]) - (b[1] - a[1]) * (c[0] - a[0]); }
    for (const f of g.fans) {
      ok('fan ' + f[0] + ' straight', f.every((p, i) => i < 2 || cross(g.PTS[f[0]], g.PTS[f[1]], g.PTS[p]) === 0));
    }
    ok('horizontal line straight', cross(g.PTS[2], g.PTS[3], g.PTS[4]) === 0);
    ok('edge line straight', cross(g.PTS[1], g.PTS[7], g.PTS[13]) === 0);
    // a non-line is NOT a capture line
    ok('false collinearity rejected', cross(g.PTS[0], g.PTS[1], g.PTS[6]) !== 0);

    // capture precomputation
    ok('apex can capture over 2 to 8', g.capTable[0].some(c => c.goat === 2 && c.land === 8));
    ok('apex can capture over 4 to 10', g.capTable[0].some(c => c.goat === 4 && c.land === 10));
    ok('8 can capture over 2 to 0', g.capTable[8].some(c => c.goat === 2 && c.land === 0));
    ok('horizontal capture 3 over 4 to 5', g.capTable[3].some(c => c.goat === 4 && c.land === 5));
    ok('edge capture 1 over 7 to 13', g.capTable[1].some(c => c.goat === 7 && c.land === 13));

    // start state
    let s = createGame({});
    eq('tigers start apex+inner', JSON.stringify(s.tigers), JSON.stringify(TIGER_START));
    ok('goats move first', s.turn === 'goat');
    ok('placement phase at start', s.phase === 'place');

    // goat placement rules
    ok('can place on empty point 1', getGoatMoves(s).some(m => m.type === 'place' && m.to === 1));
    ok('cannot place on a tiger point', !getGoatMoves(s).some(m => m.to === 0));
    s.goats.add(6);
    ok('cannot place on occupied goat point', !getGoatMoves(s).some(m => m.to === 6));
    s.goats.delete(6);

    // immediate capture: place a goat at 2 — the apex tiger eats it (jump to 8)
    applyGoatMove(s, { type: 'place', to: 2 });
    const ic = immediateCapture(s, 2);
    ok('immediate capture detected for placed goat', !!ic && ic.tiger === 0 && ic.to === 8);
    ok('immediate capture removes goat + counts', applyTigerMove(s, ic)[0].total === 1);

    // tiger capture legality
    s = createGame({});
    s.goats.add(2);                                     // goat at 2, landing 8 empty
    let tm = getTigerMoves(s);
    ok('tiger can capture goat at 2', tm.some(m => m.type === 'capture' && m.tiger === 0 && m.goat === 2 && m.to === 8));
    s.goats.add(8);                                     // now landing occupied → that capture is gone
    ok('capture blocked when landing occupied', !getTigerMoves(s).some(m => m.type === 'capture' && m.goat === 2 && m.to === 8));
    s.goats.delete(8);

    // simple tiger move
    ok('tiger can step to empty 5', getTigerMoves(s).some(m => m.type === 'move' && m.tiger === 0 && m.to === 5));

    // phase flips after 15 placements
    s = createGame({});
    for (let i = 0; i < 15; i++) { const mv = getGoatMoves(s).find(m => m.type === 'place'); applyGoatMove(s, mv); }
    ok('phase becomes move after 15 goats', s.phase === 'move');
    ok('goats can move in move phase', getGoatMoves(s).some(m => m.type === 'move'));

    // tigers win at 5 captures
    s = createGame({});
    s.captures = 4;
    s.goats.add(2);
    const capMove = getTigerMoves(s).find(m => m.type === 'capture');
    applyTigerMove(s, capMove);
    ok('tigers win on 5th capture', s.over && s.winner === 'tigers');

    // goats win when tigers are immobilised
    s = createGame({});
    s.tigers = [1, 6, 18];
    s.goats = new Set([2, 3, 4, 5, 6, 7, 12, 13, 16, 17]);
    ok('tigers blocked detected', tigersBlocked(s));
    ok('goats win when tigers blocked', checkGoatsWin(s) && s.winner === 'goats');

    // AI sanity
    s = createGame({});
    const ga = aiChooseGoatPlacement(s);
    ok('goat AI returns a placement', ga && ga.type === 'place' && !s.tigers.includes(ga.to));
    s.goats.add(2);
    const ta = aiChooseTigerMove(s);
    ok('tiger AI prefers a capture', ta && ta.type === 'capture' && ta.goat === 2);
    s.goats.add(8); s.goats.add(1);                     // seal every escape → no captures left
    const ta2 = aiChooseTigerMove(s);
    ok('tiger AI falls back to moves', ta2 && ta2.type === 'move');

    const failed = results.filter(r => !r.pass);
    const lines = results.map(r => (r.pass ? 'PASS' : 'FAIL') + '  ' + r.name);
    return { total: results.length, passed: results.length - failed.length, failed: failed.length, lines };
  }

  return {
    buildGeometry, createGame, emptyPoints, getGoatMoves, getTigerMoves,
    capturableBy, tigersBlocked, applyGoatMove, applyTigerMove, immediateCapture,
    checkGoatsWin, aiChooseGoatPlacement, aiChooseGoatMove, aiChooseTigerMove,
    TIGER_COUNT, GOAT_COUNT, CAPTURES_TO_WIN, DRAW_ROUNDS, runSelfTests
  };
})();

if (typeof module !== 'undefined' && module.exports) {
  module.exports = AaduPuliRules;
  if (require.main === module) {
    const t = AaduPuliRules.runSelfTests();
    console.log(t.lines.join('\n'));
    console.log('---- ' + t.passed + '/' + t.total + ' passed ----');
    process.exit(t.failed > 0 ? 1 : 0);
  }
}
// ===== RULES-ENGINE-END =====
