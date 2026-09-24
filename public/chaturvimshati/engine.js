// ===== RULES-ENGINE-START =====
/* Pure game rules for Chaturvimshati Koṣṭaka (चतुर्विंशति कोष्ठक) —
   "Twenty-Four Squares", an ancient strategy game described of Bhārat in
   the Sanskrit text Krīḍākauśalya. Played on a 3×8 board (24 squares)
   with 8 pieces per player; a member of the Alquerque / Sixteen Soldiers
   family. No DOM dependencies — testable in Node. */
const ChaturvimshatiRules = (function () {
  'use strict';

  const ROWS = 3;
  const COLS = 8;
  const N = ROWS * COLS;               // 24
  const PIECES_PER_PLAYER = 8;
  const DRAW_ROUNDS = 300;             // truce if nobody breaks through

  /* Board cells are numbered row-major: cell = row*8 + col.
     Row 0 (top) belongs to player 0, row 2 (bottom) to player 1,
     row 1 (middle) starts empty. */
  function buildGeometry() {
    const adj = Array.from({ length: N }, () => []);
    const link = (a, b) => { if (a !== b && adj[a].indexOf(b) === -1) { adj[a].push(b); adj[b].push(a); } };
    // horizontal neighbours
    for (let r = 0; r < ROWS; r++) for (let c = 0; c < COLS - 1; c++) link(r * COLS + c, r * COLS + c + 1);
    // vertical neighbours
    for (let c = 0; c < COLS; c++) { link(c, COLS + c); link(COLS + c, 2 * COLS + c); }

    // capture table: for a piece at p, every {mid, land} such that
    // p–mid–land are three consecutive cells on one straight line.
    const capTable = Array.from({ length: N }, () => []);
    for (let r = 0; r < ROWS; r++) {
      for (let c = 0; c < COLS; c++) {
        const p = r * COLS + c;
        if (c >= 2) capTable[p].push({ mid: p - 1, land: p - 2 });
        if (c <= COLS - 3) capTable[p].push({ mid: p + 1, land: p + 2 });
        if (r >= 2) capTable[p].push({ mid: p - COLS, land: p - 2 * COLS });
        if (r <= ROWS - 3) capTable[p].push({ mid: p + COLS, land: p + 2 * COLS });
      }
    }

    // render coordinates in a 100×46 space (3 rows, 8 columns)
    const PTS = [];
    for (let r = 0; r < ROWS; r++) for (let c = 0; c < COLS; c++) PTS.push([6.25 + c * 12.5, 8 + r * 15]);
    return { ROWS, COLS, N, adj, capTable, PTS, PIECES_PER_PLAYER };
  }

  /* ---------------- state ---------------- */
  function createGame({ names, opts }) {
    const g = buildGeometry();
    return Object.assign({}, g, {
      names: names || ['Ivory', 'Crimson'],
      opts: Object.assign({}, opts),
      pieces: [
        [0, 1, 2, 3, 4, 5, 6, 7],        // player 0 — top row
        [16, 17, 18, 19, 20, 21, 22, 23] // player 1 — bottom row
      ],
      turn: 0,
      over: false,
      winner: null,                      // 0 | 1 | 'draw'
      rounds: 0
    });
  }

  function ownerOf(state, cell) {
    if (state.pieces[0].indexOf(cell) !== -1) return 0;
    if (state.pieces[1].indexOf(cell) !== -1) return 1;
    return -1;
  }

  /* Every maximal capture chain a piece on `from` can make by jumping over
     successive opponent pieces (the same piece keeps jumping while jumps
     exist). Returns complete chains only, each with its landing path and
     the cells of the pieces it captures. */
  function captureChains(state, p, from, pieceIdx) {
    const chains = [];
    const eaten = new Set();
    function dfs(cur, path, caps) {
      let extended = false;
      for (const cap of state.capTable[cur]) {
        if (ownerOf(state, cap.mid) !== 1 - p) continue;         // jump an enemy
        if (ownerOf(state, cap.land) !== -1) continue;           // land must be empty
        if (eaten.has(cap.mid)) continue;                        // never re-jump a piece
        eaten.add(cap.mid);
        path.push(cap.land);
        caps.push(cap.mid);
        dfs(cap.land, path, caps);
        path.pop();
        caps.pop();
        eaten.delete(cap.mid);
        extended = true;
      }
      if (!extended && caps.length > 0) {
        chains.push({ type: 'capture', piece: pieceIdx, from, path: path.slice(), captures: caps.slice(), to: path[path.length - 1] });
      }
    }
    dfs(from, [], []);
    return chains;
  }

  /* ---------------- legal moves ---------------- */
  function getMoves(state, player) {
    const p = player === undefined ? state.turn : player;
    const moves = [];
    for (let pi = 0; pi < state.pieces[p].length; pi++) {
      const from = state.pieces[p][pi];
      for (const n of state.adj[from]) {
        if (ownerOf(state, n) === -1) moves.push({ type: 'step', piece: pi, from, to: n });
      }
      for (const ch of captureChains(state, p, from, pi)) moves.push(ch);
    }
    return moves;
  }

  /* ---------------- applying moves ---------------- */
  function applyMove(state, move) {
    const p = state.turn;
    const events = [];
    if (move.type === 'step') {
      state.pieces[p][move.piece] = move.to;
      events.push({ type: 'step', piece: move.piece, from: move.from, to: move.to });
    } else {
      let cur = move.from;
      for (let i = 0; i < move.path.length; i++) {
        const to = move.path[i];
        const mid = move.captures[i];
        state.pieces[p][move.piece] = to;
        const idx = state.pieces[1 - p].indexOf(mid);
        if (idx !== -1) state.pieces[1 - p].splice(idx, 1);
        events.push({ type: 'capture', piece: move.piece, from: cur, mid, to });
        cur = to;
      }
    }
    return events;
  }

  /* The mover (state.turn) wins by wiping out the enemy or by leaving them
     with no legal move. */
  function checkWin(state) {
    const opp = 1 - state.turn;
    if (state.pieces[opp].length === 0) { state.over = true; state.winner = state.turn; return true; }
    if (getMoves(state, opp).length === 0) { state.over = true; state.winner = state.turn; return true; }
    return false;
  }

  /* Truce if the armies grind for too long. */
  function checkDraw(state) {
    if (state.rounds >= DRAW_ROUNDS) { state.over = true; state.winner = 'draw'; return true; }
    return false;
  }

  /* ---------------- AI ---------------- */
  function wouldBeCaptured(state, cell) {
    const opp = 1 - state.turn;
    for (const op of state.pieces[opp]) {
      for (const cap of state.capTable[op]) {
        if (cap.mid === cell && ownerOf(state, cap.land) === -1) return true;
      }
    }
    return false;
  }

  function aiChooseMove(state) {
    const moves = getMoves(state);
    if (!moves.length) return null;
    const caps = moves.filter(m => m.type === 'capture');
    if (caps.length) {
      let best = null, bestScore = -Infinity;
      for (const m of caps) {
        let s = m.captures.length * 10 + Math.random() * 2;
        if (wouldBeCaptured(state, m.to)) s -= 4;
        if (s > bestScore) { bestScore = s; best = m; }
      }
      return best;
    }
    let best = null, bestScore = -Infinity;
    for (const m of moves) {
      let s = Math.random() * 3;
      const fromRow = Math.floor(m.from / COLS);
      const toRow = Math.floor(m.to / COLS);
      const advance = state.turn === 0 ? (toRow - fromRow) : (fromRow - toRow);
      s += advance * 4;                                   // press toward the enemy
      if (wouldBeCaptured(state, m.to)) s -= 50;          // never walk into a jump
      const c = m.to % COLS;
      s += (3.5 - Math.abs(c - 3.5)) * 0.4;               // favour the centre columns
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
    ok('24 squares', g.N === 24);
    ok('3 rows x 8 cols', g.ROWS === 3 && g.COLS === 8);
    ok('corner degree 2', g.adj[0].length === 2 && g.adj[7].length === 2 && g.adj[16].length === 2 && g.adj[23].length === 2);
    ok('edge degree 3', g.adj[1].length === 3);
    ok('middle degree 4', g.adj[9].length === 4);   // cell 9: row 1, col 1
    ok('horizontal link', g.adj[4].indexOf(5) !== -1 && g.adj[4].indexOf(3) !== -1);
    ok('vertical link', g.adj[4].indexOf(12) !== -1);
    ok('capture horizontal 0 over 1 to 2', g.capTable[0].some(c => c.mid === 1 && c.land === 2));
    ok('capture vertical 0 over 8 to 16', g.capTable[0].some(c => c.mid === 8 && c.land === 16));
    ok('no diagonal capture', !g.capTable[0].some(c => c.mid === 9));

    // start state
    let s = createGame({});
    eq('ivory on top row', JSON.stringify(s.pieces[0]), JSON.stringify([0, 1, 2, 3, 4, 5, 6, 7]));
    eq('crimson on bottom row', JSON.stringify(s.pieces[1]), JSON.stringify([16, 17, 18, 19, 20, 21, 22, 23]));
    ok('middle row empty', [8, 9, 10, 11, 12, 13, 14, 15].every(c => ownerOf(s, c) === -1));
    ok('ivory moves first', s.turn === 0);

    // step moves — a corner piece can always step down into the empty middle row
    ok('corner can step down', getMoves(s, 0).some(m => m.type === 'step' && m.piece === 0 && m.to === 8));
    ok('horizontal step blocked by own neighbour', !getMoves(s, 0).some(m => m.type === 'step' && m.piece === 0 && m.to === 1));

    // single capture: 0 over 1 to 2 (enemy at 1, landing 2 empty)
    s = createGame({});
    s.pieces[0] = [0];
    s.pieces[1] = [1, 16];
    const caps = getMoves(s, 0).filter(m => m.type === 'capture');
    ok('single capture available', caps.some(m => m.piece === 0 && m.captures.length === 1 && m.to === 2));
    applyMove(s, { type: 'capture', piece: 0, from: 0, path: [2], captures: [1], to: 2 });
    ok('captured piece removed', s.pieces[1].indexOf(1) === -1);
    ok('piece landed at 2', s.pieces[0][0] === 2);

    // multi-capture chain: 0 over 1 to 2, then over 3 to 4
    s = createGame({});
    s.pieces[0] = [0];
    s.pieces[1] = [1, 3];
    const chain = getMoves(s, 0).find(m => m.type === 'capture' && m.piece === 0);
    ok('multi-capture chain found', chain && chain.captures.length === 2 && chain.to === 4);
    applyMove(s, chain);
    ok('multi-capture removes both', s.pieces[1].indexOf(1) === -1 && s.pieces[1].indexOf(3) === -1);
    ok('chain ends at 4', s.pieces[0][0] === 4);

    // win by capture-all
    s = createGame({});
    s.pieces[1] = [];
    ok('win by capture all', checkWin(s) && s.over && s.winner === 0);

    // win by blocking: crimson piece at 0, ivory seals every escape & landing
    s = createGame({});
    s.pieces[1] = [0];
    s.pieces[0] = [1, 2, 8, 16];
    s.turn = 0;
    ok('blocked player has no moves', getMoves(s, 1).length === 0);
    ok('win by blocking', checkWin(s) && s.over && s.winner === 0);

    // AI sanity
    s = createGame({});
    const ai = aiChooseMove(s);
    ok('AI returns a move', ai && (ai.type === 'step' || ai.type === 'capture'));
    s = createGame({});
    s.pieces[0] = [0];
    s.pieces[1] = [1];
    const ai2 = aiChooseMove(s);
    ok('AI prefers a capture', ai2 && ai2.type === 'capture');

    const failed = results.filter(r => !r.pass);
    const lines = results.map(r => (r.pass ? 'PASS' : 'FAIL') + '  ' + r.name);
    return { total: results.length, passed: results.length - failed.length, failed: failed.length, lines };
  }

  return {
    buildGeometry, createGame, ownerOf, getMoves, applyMove, checkWin, checkDraw,
    captureChains, wouldBeCaptured, aiChooseMove,
    ROWS, COLS, N, PIECES_PER_PLAYER, DRAW_ROUNDS, runSelfTests
  };
})();

if (typeof module !== 'undefined' && module.exports) {
  module.exports = ChaturvimshatiRules;
  if (require.main === module) {
    const t = ChaturvimshatiRules.runSelfTests();
    console.log(t.lines.join('\n'));
    console.log('---- ' + t.passed + '/' + t.total + ' passed ----');
    process.exit(t.failed > 0 ? 1 : 0);
  }
}
// ===== RULES-ENGINE-END =====
