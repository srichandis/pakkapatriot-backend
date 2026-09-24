// ===== RULES-ENGINE-START =====
/* Pure game rules for Vish & Amrit (विष & अमृत) — a chase game.
   One player is VISH (the poison chaser). Three runners flee him.
   If Vish touches a runner, that runner freezes into a STATUE.
   A free runner that ends its move touching a statue says "AMRIT"
   and releases it back into the game.
   Vish wins by freezing all three runners; the runners win by
   surviving ROUND_LIMIT rounds without being fully frozen.
   No DOM dependencies — testable in Node. */
const VishAmritRules = (function () {
  'use strict';

  const N = 7;                      // 7×7 board
  const SIZE = N * N;               // 49 cells
  const RUNNERS = 3;                // number of fleeing runners
  const ROUND_LIMIT = 60;           // runners win if they survive this many rounds

  // 8 king directions (orthogonal + diagonal)
  const DIRS = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];

  function buildGeometry() {
    const adj = Array.from({ length: SIZE }, () => []);
    for (let r = 0; r < N; r++) {
      for (let c = 0; c < N; c++) {
        const p = r * N + c;
        for (const [dr, dc] of DIRS) {
          const nr = r + dr, nc = c + dc;
          if (nr >= 0 && nr < N && nc >= 0 && nc < N) adj[p].push(nr * N + nc);
        }
      }
    }
    // Vish's poison leap: two squares in a straight line (any of the 8 directions),
    // leaping over anything in between. This is what lets the chaser outrun the runners.
    const leaps = Array.from({ length: SIZE }, () => []);
    for (let r = 0; r < N; r++) {
      for (let c = 0; c < N; c++) {
        const p = r * N + c;
        for (const [dr, dc] of DIRS) {
          const nr = r + 2 * dr, nc = c + 2 * dc;
          if (nr >= 0 && nr < N && nc >= 0 && nc < N) leaps[p].push(nr * N + nc);
        }
      }
    }
    // render coordinates in a 100×100 space (7×7 grid)
    const PTS = [];
    for (let r = 0; r < N; r++) for (let c = 0; c < N; c++) PTS.push([12.5 + c * 12.5, 12.5 + r * 12.5]);
    return { N, SIZE, adj, leaps, PTS, RUNNERS, ROUND_LIMIT };
  }

  /* ---------------- state ---------------- */
  function createGame({ names, opts }) {
    const g = buildGeometry();
    return Object.assign({}, g, {
      names: names || ['Amrit (Runners)', 'Vish'],
      opts: Object.assign({}, opts),
      // Vish starts in the centre; the three runners flee from three corners.
      vish: 24,
      runners: [
        { id: 0, cell: 0, statue: false },
        { id: 1, cell: 6, statue: false },
        { id: 2, cell: 48, statue: false }
      ],
      turn: 0,                      // 0 = runners, 1 = vish
      round: 0,
      over: false,
      winner: null                  // 0 = runners (survived), 1 = vish (froze all)
    });
  }

  function runnerAt(state, cell) {
    return state.runners.find(r => r.cell === cell);
  }
  function freeRunners(state) { return state.runners.filter(r => !r.statue); }
  function statues(state) { return state.runners.filter(r => r.statue); }
  function isAdjacent(state, a, b) { return state.adj[a].indexOf(b) !== -1; }

  /* ---------------- legal moves ---------------- */
  /* A free runner may step one king-square. It may not step onto Vish or onto a
     statue (frozen blocks), nor onto another free runner. Ending a move touching
     (king-adjacent to) any statue releases it — that is the AMRIT act. */
  function getRunnerMoves(state, idx) {
    const r = state.runners[idx];
    const moves = [];
    if (!r || r.statue) return moves;
    for (const n of state.adj[r.cell]) {
      if (n === state.vish) continue;
      if (runnerAt(state, n)) continue;                  // any runner (free or statue) blocks
      const freed = state.runners.filter(s => s.statue && isAdjacent(state, n, s.cell)).map(s => s.id);
      moves.push({ type: 'runner', idx, from: r.cell, to: n, frees: freed });
    }
    return moves;
  }

  /* Vish may step one king-square or leap two squares in a straight line.
     It may not land on a statue. Landing on a free runner freezes it. */
  function getVishMoves(state) {
    const from = state.vish;
    const seen = new Set([from]);
    const moves = [];
    for (const n of state.adj[from]) {
      if (seen.has(n)) continue;
      seen.add(n);
      if (runnerAt(state, n) && runnerAt(state, n).statue) continue;   // statues block
      const hit = runnerAt(state, n);
      moves.push({ type: 'vish', from, to: n, captures: hit && !hit.statue ? hit.id : null });
    }
    for (const n of state.leaps[from]) {
      if (seen.has(n)) continue;
      seen.add(n);
      if (runnerAt(state, n) && runnerAt(state, n).statue) continue;
      const hit = runnerAt(state, n);
      moves.push({ type: 'vish', from, to: n, captures: hit && !hit.statue ? hit.id : null });
    }
    return moves;
  }

  /* ---------------- applying moves ---------------- */
  /* Amrit releases are derived from the board, never from client-supplied data:
     any statue king-adjacent to the landing cell is freed. */
  function applyRunnerMove(state, move) {
    const r = state.runners[move.idx];
    const freedIds = state.runners.filter(s => s.statue && isAdjacent(state, move.to, s.cell)).map(s => s.id);
    r.cell = move.to;
    const events = [{ type: 'runner', idx: move.idx, from: move.from, to: move.to, freed: freedIds }];
    for (const id of freedIds) { state.runners[id].statue = false; }
    return events;
  }

  function applyVishMove(state, move) {
    state.vish = move.to;
    const hit = runnerAt(state, move.to);
    let captured = null;
    if (hit && !hit.statue) { hit.statue = true; captured = hit.id; }
    return [{ type: 'vish', from: move.from, to: move.to, captured }];
  }

  /* Dispatcher used by the UI and any caller holding only a generic move. */
  function applyMove(state, move) {
    return move.type === 'vish' ? applyVishMove(state, move) : applyRunnerMove(state, move);
  }

  /* Vish wins if every runner is a statue. The runners win by surviving
     ROUND_LIMIT rounds. Returns true and sets over/winner when finished. */
  function checkWin(state) {
    if (state.runners.every(r => r.statue)) { state.over = true; state.winner = 1; return true; }
    if (state.round >= state.ROUND_LIMIT) { state.over = true; state.winner = 0; return true; }
    return false;
  }

  /* ---------------- AI ---------------- */
  function dist(a, b) {
    const ar = Math.floor(a / N), ac = a % N, br = Math.floor(b / N), bc = b % N;
    return Math.max(Math.abs(ar - br), Math.abs(ac - bc));
  }

  /* The computer chaser: always take a capture when available; otherwise
     sprint toward the nearest free runner. */
  function aiVish(state) {
    const moves = getVishMoves(state);
    if (!moves.length) return null;
    const free = freeRunners(state);
    let best = null, bestScore = -Infinity;
    for (const m of moves) {
      let s = (m.captures !== null && m.captures !== undefined ? 100 : 0) + Math.random() * 1;
      const nearest = free.length ? Math.min(...free.map(r => dist(m.to, r.cell))) : 8;
      s += (8 - nearest) * 0.8;                        // close the gap on the closest runner
      if (s > bestScore) { bestScore = s; best = m; }
    }
    return best;
  }

  /* The computer runners: free statues whenever possible; otherwise scatter
     away from Vish. */
  function aiRunners(state) {
    const free = freeRunners(state);
    let best = null, bestScore = -Infinity;
    for (const r of free) {
      for (const m of getRunnerMoves(state, r.id)) {
        let s = Math.random() * 1;
        if (m.frees.length > 0) s += 80 + m.frees.length * 10;   // AMRIT!
        s += (dist(m.to, state.vish)) * 2;                       // stay far from the poison
        if (s > bestScore) { bestScore = s; best = m; }
      }
    }
    return best;
  }

  /* ---------------- self tests ---------------- */
  function runSelfTests() {
    const results = [];
    const ok = (name, cond) => results.push({ name, pass: !!cond });
    const eq = (name, a, b) => results.push({ name, pass: JSON.stringify(a) === JSON.stringify(b), a, b });

    const g = buildGeometry();
    ok('49 cells', g.SIZE === 49);
    ok('corner degree 3', g.adj[0].length === 3);
    ok('edge degree 5', g.adj[3].length === 5);
    ok('centre degree 8', g.adj[24].length === 8);
    ok('corner leaps 3', g.leaps[0].length === 3);   // down, right, down-right
    ok('centre leaps 8', g.leaps[24].length === 8);

    // start state
    let s = createGame({});
    ok('vish at centre', s.vish === 24);
    eq('three runners', s.runners.map(r => r.cell), [0, 6, 48]);
    ok('no runner starts statue', s.runners.every(r => !r.statue));
    ok('runners move first', s.turn === 0);

    // runner movement: one step, not onto vish or another runner
    const rm0 = getRunnerMoves(s, 0);
    ok('corner runner has 3 moves', rm0.length === 3);
    ok('runner cannot step onto vish(24)', !rm0.some(m => m.to === 24));
    ok('runner cannot step onto runner 1(6)', !rm0.some(m => m.to === 6));
    const rm1 = getRunnerMoves(s, 1);
    ok('runner 1 (corner) has 3 moves', rm1.length === 3);

    // apply a runner move, then vish captures
    s = createGame({});
    s.runners[0] = { id: 0, cell: 25, statue: false };   // move runner 0 next to centre
    s.vish = 24;
    const vm = getVishMoves(s).find(m => m.to === 25);
    ok('vish can capture adjacent runner', vm && vm.captures === 0);
    applyVishMove(s, vm);
    ok('runner becomes statue', s.runners[0].statue === true);
    ok('vish now on runner cell', s.vish === 25);

    // statues block: runners cannot land on a statue's cell
    s = createGame({});
    s.runners[0] = { id: 0, cell: 1, statue: true };      // statue at 1 (adjacent to corner 0)
    s.runners[1] = { id: 1, cell: 3, statue: false };     // runner elsewhere
    ok('runner cannot land on statue cell', !getRunnerMoves(s, 0).some(m => m.to === 1));

    // AMRIT: ending a move adjacent to a statue releases it
    s = createGame({});
    s.runners[0] = { id: 0, cell: 2, statue: true };      // statue at 2
    s.runners[1] = { id: 1, cell: 0, statue: false };     // runner at corner 0
    const am = getRunnerMoves(s, 1).find(m => m.to === 8); // 8 (1,1) is adjacent to statue at 2
    ok('amrit move offered', am && am.frees.indexOf(0) !== -1);
    applyRunnerMove(s, am);
    ok('statue released by amrit', s.runners[0].statue === false);
    ok('runner moved to 8', s.runners[1].cell === 8);

    // vish leap: two squares in a straight line
    s = createGame({});
    s.vish = 24;
    s.runners[0] = { id: 0, cell: 40, statue: false };    // (5,5) = 2 diagonal from (3,3)
    const leap = getVishMoves(s).find(m => m.to === 40);
    ok('vish can leap 2 to capture', leap && leap.captures === 0);
    ok('vish cannot land on statue', !getVishMoves(s).some(m => { const r = runnerAt(s, m.to); return r && r.statue; }));

    // win: freeze all runners -> vish wins
    s = createGame({});
    s.runners.forEach(r => { r.statue = true; });
    ok('vish wins when all frozen', checkWin(s) && s.over && s.winner === 1);

    // win: survival round limit -> runners win
    s = createGame({});
    s.round = 59;
    s.turn = 1;
    // simulate vish move that freezes nobody, then round reaches 60
    s.vish = 40;
    s.runners[0] = { id: 0, cell: 0, statue: false };
    s.runners[1] = { id: 1, cell: 6, statue: false };
    s.runners[2] = { id: 2, cell: 48, statue: false };
    const mv = getVishMoves(s).find(m => m.to === 32);    // land somewhere harmless
    applyVishMove(s, mv);
    s.round = 60;
    ok('runners win by surviving', checkWin(s) && s.over && s.winner === 0);

    // AI sanity
    s = createGame({});
    const ai1 = aiVish(s);
    ok('vish AI returns a move', ai1 && ai1.type === 'vish');
    s.runners[0] = { id: 0, cell: 25, statue: false };    // capture available
    s.vish = 24;
    const ai2 = aiVish(s);
    ok('vish AI takes a capture', ai2 && ai2.captures === 0);
    s = createGame({});
    s.runners[0] = { id: 0, cell: 2, statue: true };      // amrit available
    s.runners[1] = { id: 1, cell: 0, statue: false };
    const ai3 = aiRunners(s);
    ok('runner AI frees a statue', ai3 && ai3.frees && ai3.frees.length > 0);

    const failed = results.filter(r => !r.pass);
    const lines = results.map(r => (r.pass ? 'PASS' : 'FAIL') + '  ' + r.name + (r.pass ? '' : '  got=' + JSON.stringify(r.a) + ' want=' + JSON.stringify(r.b)));
    return { total: results.length, passed: results.length - failed.length, failed: failed.length, lines };
  }

  return {
    buildGeometry, createGame, runnerAt, freeRunners, statues, isAdjacent,
    getRunnerMoves, getVishMoves, applyRunnerMove, applyVishMove, applyMove, checkWin,
    aiVish, aiRunners, dist,
    N, SIZE, RUNNERS, ROUND_LIMIT, runSelfTests
  };
})();

if (typeof module !== 'undefined' && module.exports) {
  module.exports = VishAmritRules;
  if (require.main === module) {
    const t = VishAmritRules.runSelfTests();
    console.log(t.lines.join('\n'));
    console.log('---- ' + t.passed + '/' + t.total + ' passed ----');
    process.exit(t.failed > 0 ? 1 : 0);
  }
}
// ===== RULES-ENGINE-END =====
