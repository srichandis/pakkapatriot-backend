// ===== RULES-ENGINE-START =====
/* Pure game rules for Pachisi (पचीसी / "Twenty-Five") — the cross-board
   race game of ancient Bhārat, played with seven cowrie shells. The game is
   named for its largest throw, pachees (25). No DOM dependencies — testable
   in Node.

   BOARD: a 15×15 grid. The Charkoni (central 3×3, rows/cols 6-8) is the
   start and finish. Four arms of 3 columns × 8 rows meet at the Charkoni.
   Each player owns one arm's middle column (the home column). A piece:
     1) enters on square 1 (the tip of its own arm — a castle square),
     2) travels down its own home column,
     3) loops counter-clockwise around the 52 outer squares,
     4) climbs back up its home column and enters the Charkoni
        (exact throw required).

   THROW (7 cowrie shells, mouths-up count):
       0 = 7 (Sat)     1 = 10 (Dus)    2 = 2 (Dooga)    3 = 3 (Teeni)
       4 = 4 (Chari)   5 = 25 (Pachees) 6 = 35 (Paintees) 7 = 14 (Chaudah)
   Grace throws (extra turn): 7, 10, 25, 35, 14. A 10/25/35 may also bring
   a fresh piece from the Charkoni onto square 1.

   CAPTURE: landing on any number of opponents (except on a castle square)
   captures them all — they return to the Charkoni and you throw again.
   CASTLES (✕): the 4 arm tips + 8 squares four-in from the arm ends.
   A piece may not land on a castle held by an opponent. */
const PachisiRules = (function () {
  'use strict';

  const N = 15;           // 15×15 grid
  const PIECES = 4;       // four pieces per player
  const LAST = 60;        // path index of the Charkoni centre (finish)

  /* rotate a coordinate 90° clockwise about the board centre */
  function rotCW(pt, times) {
    let r = pt[0], c = pt[1];
    for (let t = 0; t < times; t++) { const nr = c, nc = N - 1 - r; r = nr; c = nc; }
    return [r, c];
  }
  function cellKey(pt) { return pt[0] + ',' + pt[1]; }

  /* The full path for the top-arm (red) player, in the order a piece
     travels it: down the home column, around the 52-square loop
     counter-clockwise, back up the home column into the Charkoni. */
  function redPathCells() {
    const homeDown = [[0, 7], [1, 7], [2, 7], [3, 7], [4, 7], [5, 7]];
    const loop = [
      // left arm (top row out, tip, bottom row back)
      [5, 6], [6, 5], [6, 4], [6, 3], [6, 2], [6, 1], [6, 0], [7, 0],
      [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5],
      // bottom arm (left column down, tip, right column up)
      [9, 6], [10, 6], [11, 6], [12, 6], [13, 6], [14, 6], [14, 7], [14, 8],
      [13, 8], [12, 8], [11, 8], [10, 8], [9, 8],
      // right arm (bottom row out, tip, top row back)
      [8, 9], [8, 10], [8, 11], [8, 12], [8, 13], [8, 14], [7, 14], [6, 14],
      [6, 13], [6, 12], [6, 11], [6, 10], [6, 9],
      // top arm (right column up, tip, left column down)
      [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8], [0, 7], [0, 6],
      [1, 6], [2, 6], [3, 6], [4, 6]
    ];
    const homeUp = [[5, 7], [6, 7], [7, 7]];   // ends on the Charkoni centre
    return homeDown.concat(loop, homeUp);      // 6 + 52 + 3 = 61 cells
  }

  /* the 12 castle (safe) squares: 4 arm tips + 8 four-in from the arm ends */
  function castleKeys() {
    return new Set([
      '0,7', '7,14', '14,7', '7,0',                       // tips
      '3,8', '3,6', '8,11', '6,11', '11,8', '11,6', '6,3', '8,3'
    ]);
  }

  function buildGeometry() {
    const redPath = redPathCells();
    const paths = [];
    for (let k = 0; k < 4; k++) paths.push(redPath.map(pt => rotCW(pt, k)));
    const castles = castleKeys();

    // every rendered cell of the cross (81 = 52 loop + 20 home + 9 Charkoni)
    const cells = [];
    const seen = new Set();
    const push = (r, c, kind) => {
      const key = r + ',' + c;
      if (seen.has(key)) return;
      seen.add(key);
      cells.push({ r, c, kind, castle: castles.has(key) });
    };
    redPath.slice(6, 58).forEach(pt => push(pt[0], pt[1], 'loop'));
    // home-column cells of each arm (the 5 non-tip cells between tip & Charkoni)
    for (let k = 0; k < 4; k++) {
      for (let i = 1; i <= 5; i++) {
        const pt = rotCW([i, 7], k);
        push(pt[0], pt[1], 'home');
      }
    }
    for (let r = 6; r <= 8; r++) for (let c = 6; c <= 8; c++) push(r, c, 'charkoni');

    return { N, paths, castles, cells, last: LAST };
  }

  /* ---- dice ---- */
  const THROW_TABLE = [
    { value: 7, label: 'Sat', up: 0 },
    { value: 10, label: 'Dus', up: 1 },
    { value: 2, label: 'Dooga', up: 2 },
    { value: 3, label: 'Teeni', up: 3 },
    { value: 4, label: 'Chari', up: 4 },
    { value: 25, label: 'Pachees', up: 5 },
    { value: 35, label: 'Paintees', up: 6 },
    { value: 14, label: 'Chaudah', up: 7 }
  ];
  function roll() {
    const mouths = [0, 1, 2, 3, 4, 5, 6].map(() => Math.random() < 0.5);
    return rollFromMouths(mouths);
  }
  function rollFromMouths(mouths) {
    const up = mouths.filter(Boolean).length;
    const t = THROW_TABLE[up];
    return {
      mouths, up,
      value: t.value,
      label: t.label,
      // 7, 10, 25, 35, 14 all grant a second throw
      bonus: up === 0 || up === 1 || up === 5 || up === 6 || up === 7,
      // 10, 25, 35 allow bringing a piece from the Charkoni onto square 1
      canEnter: t.value === 10 || t.value === 25 || t.value === 35
    };
  }

  /* Which physical arms (0=top, 1=right, 2=bottom, 3=left) each player uses */
  function armsFor(playerCount) {
    if (playerCount === 2) return [0, 2];      // opposite arms
    if (playerCount === 3) return [0, 1, 2];   // one arm rests
    return [0, 1, 2, 3];
  }
  function createGame({ playerCount, names, opts }) {
    const g = buildGeometry();
    const arms = armsFor(playerCount);
    const players = [];
    for (let i = 0; i < playerCount; i++) {
      players.push({
        idx: i,
        arm: arms[i],
        name: (names && names[i]) || 'Player ' + (i + 1),
        kills: 0,
        finished: 0,
        pieces: Array.from({ length: PIECES }, () => ({ status: 'base', pos: -1 }))
      });
    }
    return Object.assign({}, g, {
      playerCount, arms, pieceCount: PIECES,
      opts: Object.assign({}, opts),
      players, cur: 0,
      over: false, winner: -1
    });
  }

  /* ---- helpers ---- */
  function piecesAt(state, pt) {
    const key = cellKey(pt);
    const res = [];
    for (const pl of state.players) for (const pc of pl.pieces) {
      if (pc.status === 'on' && cellKey(state.paths[pl.arm][pc.pos]) === key) {
        res.push({ player: pl, piece: pc });
      }
    }
    return res;
  }
  function isCastle(state, pt) { return state.castles.has(cellKey(pt)); }

  /* Legal moves for the current player with the given roll:
       { type:'enter', pieceIdx }      — bring a base piece onto square 1
       { type:'move', pieceIdx, from, to, captures, finish } */
  function getLegalMoves(state, roll) {
    const pl = state.players[state.cur];
    const path = state.paths[pl.arm];
    const moves = [];

    // entering — only on a 10/25/35 grace, and square 1 (a castle) must be free
    if (roll.canEnter) {
      for (let i = 0; i < pl.pieces.length; i++) {
        const pc = pl.pieces[i];
        if (pc.status !== 'base') continue;
        const foes = piecesAt(state, path[0]).filter(o => o.player.idx !== pl.idx);
        if (foes.length > 0) continue;   // can't land on an opponent-held castle
        moves.push({ type: 'enter', pieceIdx: i });
      }
    }

    // moving — exact count, finish by exact throw, castle held by foe blocks
    for (let i = 0; i < pl.pieces.length; i++) {
      const pc = pl.pieces[i];
      if (pc.status !== 'on') continue;
      const d = pc.pos + roll.value;
      if (d > state.last) continue;      // overshoots the Charkoni — illegal
      const landPt = path[d];
      const foes = piecesAt(state, landPt).filter(o => o.player.idx !== pl.idx);
      if (isCastle(state, landPt) && foes.length > 0) continue;
      moves.push({
        type: 'move', pieceIdx: i, from: pc.pos, to: d,
        captures: foes.length,
        finish: d === state.last
      });
    }
    return moves;
  }

  /* Apply a move. Returns { events, captured } where events include
     {type:'enter'}, {type:'move', to, finished} and {type:'capture', count}. */
  function applyMove(state, move) {
    const pl = state.players[state.cur];
    const events = [];
    let captured = false;

    if (move.type === 'enter') {
      const pc = pl.pieces[move.pieceIdx];
      if (pc.status !== 'base') return { events, captured };
      pc.status = 'on';
      pc.pos = 0;
      events.push({ type: 'enter' });
      return { events, captured };
    }

    const pc = pl.pieces[move.pieceIdx];
    if (pc.status !== 'on') return { events, captured };
    pc.pos = move.to;
    if (move.to === state.last) {
      pc.status = 'done';
      pl.finished++;
      events.push({ type: 'move', to: move.to, finished: 1 });
      return { events, captured };       // entering the Charkoni — no captures
    }

    const landPt = state.paths[pl.arm][move.to];
    const foes = piecesAt(state, landPt).filter(o => o.player.idx !== pl.idx);
    if (foes.length > 0 && !isCastle(state, landPt)) {
      for (const o of foes) {
        o.piece.status = 'base';
        o.piece.pos = -1;
      }
      pl.kills += foes.length;
      captured = true;
      events.push({ type: 'capture', count: foes.length });
    }
    events.push({ type: 'move', to: move.to, finished: 0 });
    return { events, captured };
  }

  function checkWin(state) {
    const pl = state.players[state.cur];
    if (pl.finished === state.pieceCount) { state.over = true; state.winner = state.cur; return true; }
    return false;
  }

  /* ---- self tests ---- */
  function runSelfTests() {
    const results = [];
    const ok = (name, cond) => results.push({ name, pass: !!cond });
    const eq = (name, a, b) => results.push({ name, pass: JSON.stringify(a) === JSON.stringify(b), a, b });

    const g = buildGeometry();
    ok('red path length 61', g.paths[0].length === 61);
    ok('all paths end on Charkoni centre', g.paths.every(p => cellKey(p[60]) === '7,7'));
    ok('red tip is square 1', cellKey(g.paths[0][0]) === '0,7');
    eq('rotation to yellow', JSON.stringify(g.paths[1]), JSON.stringify(g.paths[0].map(pt => rotCW(pt, 1))));
    ok('path cells distinct (home re-entry shared)', new Set(g.paths[0].map(cellKey)).size === 59);
    ok('12 castle squares', g.castles.size === 12);
    ok('tips are castles', ['0,7', '7,14', '14,7', '7,0'].every(k => g.castles.has(k)));
    ok('four-in squares are castles', ['3,8', '3,6', '8,11', '6,11', '11,8', '11,6', '6,3', '8,3'].every(k => g.castles.has(k)));
    ok('81 board cells', g.cells.length === 81);
    ok('charkoni cells present', g.cells.filter(c => c.kind === 'charkoni').length === 9);

    // dice scoring (7-shell table)
    const want = [
      { up: 0, v: 7, label: 'Sat', bonus: true, enter: false },
      { up: 1, v: 10, label: 'Dus', bonus: true, enter: true },
      { up: 2, v: 2, label: 'Dooga', bonus: false, enter: false },
      { up: 3, v: 3, label: 'Teeni', bonus: false, enter: false },
      { up: 4, v: 4, label: 'Chari', bonus: false, enter: false },
      { up: 5, v: 25, label: 'Pachees', bonus: true, enter: true },
      { up: 6, v: 35, label: 'Paintees', bonus: true, enter: true },
      { up: 7, v: 14, label: 'Chaudah', bonus: true, enter: false }
    ];
    want.forEach((w, i) => {
      const mouths = Array.from({ length: 7 }, (_, j) => j < w.up);
      const r = rollFromMouths(mouths);
      ok('throw ' + w.up + ' value ' + w.v, r.value === w.v && r.up === w.up);
      ok('throw ' + w.up + ' label ' + w.label, r.label === w.label);
      ok('throw ' + w.up + ' bonus=' + w.bonus, r.bonus === w.bonus);
      ok('throw ' + w.up + ' enter=' + w.enter, r.canEnter === w.enter);
    });

    // arms for player counts
    eq('2P opposite arms', JSON.stringify(armsFor(2)), '[0,2]');
    eq('3P arms', JSON.stringify(armsFor(3)), '[0,1,2]');
    eq('4P arms', JSON.stringify(armsFor(4)), '[0,1,2,3]');

    // entry rules
    let s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    let m10 = getLegalMoves(s, rollFromMouths([1, 0, 0, 0, 0, 0, 0]));   // Dus
    ok('Dus allows entry', m10.some(m => m.type === 'enter'));
    let m2 = getLegalMoves(s, rollFromMouths([0, 1, 1, 0, 0, 0, 0]));    // Dooga
    ok('Dooga does not allow entry', !m2.some(m => m.type === 'enter'));
    let m7 = getLegalMoves(s, rollFromMouths([0, 0, 0, 0, 0, 0, 0]));    // Sat
    ok('Sat is a grace but no entry', m7.some(m => m.type === 'enter') === false && rollFromMouths([0,0,0,0,0,0,0]).bonus === true);

    // movement
    s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    s.players[0].pieces[0] = { status: 'on', pos: 3 };
    ok('move 3+4 -> 7', getLegalMoves(s, rollFromMouths([0,1,1,1,1,0,0])).some(m => m.type === 'move' && m.to === 7));
    // overshoot illegal
    s.players[0].pieces[0] = { status: 'on', pos: 58 };
    ok('overshoot centre illegal', !getLegalMoves(s, rollFromMouths([0,1,0,0,0,0,0])).some(m => m.type === 'move' && m.pieceIdx === 0));
    // exact throw finishes
    s.players[0].pieces[0] = { status: 'on', pos: 58 };
    const fin = getLegalMoves(s, rollFromMouths([0,1,1,1,1,0,0]));      // Chari = 4 → 58+4=62 illegal
    ok('58+4 overshoot', !fin.some(m => m.type === 'move' && m.pieceIdx === 0));
    s.players[0].pieces[0] = { status: 'on', pos: 59 };
    const fin2 = getLegalMoves(s, rollFromMouths([1,0,0,0,0,0,0]));     // Dus = 10 → 69 illegal
    ok('59+10 overshoot', !fin2.some(m => m.type === 'move' && m.pieceIdx === 0));
    s.players[0].pieces[0] = { status: 'on', pos: 58 };
    const fin3 = getLegalMoves(s, rollFromMouths([0,0,1,1,0,0,0]));     // Dooga = 2 → 60 = finish
    ok('exact throw to centre', fin3.some(m => m.type === 'move' && m.pieceIdx === 0 && m.to === 60 && m.finish));

    // capture: A lands on B on a shared loop cell (non-castle) — all of B's
    // pieces stacked there return to base
    s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    const landPt = s.paths[0][10];                                      // (6,2) — shared loop cell
    const bIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(landPt));
    ok('found B index', bIdx !== -1);
    ok('loop cell is shared', s.castles.has(cellKey(landPt)) === false);
    s.players[0].pieces[0] = { status: 'on', pos: 8 };                  // (6,4) — loop cell
    s.players[1].pieces[0] = { status: 'on', pos: bIdx };
    s.players[1].pieces[1] = { status: 'on', pos: bIdx };               // B has two pieces stacked
    const captMove = getLegalMoves(s, rollFromMouths([0, 0, 1, 1, 0, 0, 0])).find(m => m.type === 'move' && m.pieceIdx === 0);  // Dooga = 2 → 8+2 = 10
    ok('capture move present', captMove && captMove.captures === 2);
    const r = applyMove(s, { type: 'move', pieceIdx: 0, from: 8, to: 10 });
    ok('capture happened', r.captured);
    ok('both B pieces return to base', s.players[1].pieces[0].status === 'base' && s.players[1].pieces[1].status === 'base');
    ok('A kills incremented', s.players[0].kills === 2);

    // castle: landing on an opponent-held castle is illegal
    s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    const castlePt = s.paths[0][52];                                    // (0,7) top tip — a castle
    ok('cell 52 is castle', s.castles.has(cellKey(castlePt)));
    const bCastleIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(castlePt));
    ok('foe can stand on the castle', bCastleIdx !== -1);
    s.players[1].pieces[0] = { status: 'on', pos: bCastleIdx };
    s.players[0].pieces[0] = { status: 'on', pos: 50 };
    const cMoves = getLegalMoves(s, rollFromMouths([0,0,1,1,0,0,0]));   // Dooga = 2 → 50+2=52 castle held by B
    ok('cannot land on foe castle', !cMoves.some(m => m.type === 'move' && m.pieceIdx === 0));

    // entering blocked when opponent holds the tip castle
    s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    const tipPt = s.paths[0][0];                                        // (0,7)
    const bTipIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(tipPt));
    s.players[1].pieces[0] = { status: 'on', pos: bTipIdx };
    const eMoves = getLegalMoves(s, rollFromMouths([1, 0, 0, 0, 0, 0, 0]));  // Dus — enter blocked
    ok('entry blocked by foe on tip', !eMoves.some(m => m.type === 'enter'));

    // win detection
    s = createGame({ playerCount: 2, names: ['A', 'B'], opts: {} });
    s.players[0].finished = 4;
    ok('win detected', checkWin(s) && s.winner === 0);

    const failed = results.filter(r => !r.pass);
    const lines = results.map(r => (r.pass ? 'PASS' : 'FAIL') + '  ' + r.name + (r.pass ? '' : '  got=' + JSON.stringify(r.a) + ' want=' + JSON.stringify(r.b)));
    return { total: results.length, passed: results.length - failed.length, failed: failed.length, lines };
  }

  return {
    buildGeometry, armsFor, roll, rollFromMouths, createGame,
    getLegalMoves, applyMove, checkWin,
    piecesAt, isCastle, cellKey, rotCW,
    runSelfTests
  };
})();

if (typeof module !== 'undefined' && module.exports) {
  module.exports = PachisiRules;
  if (require.main === module) {
    const t = PachisiRules.runSelfTests();
    console.log(t.lines.join('\n'));
    console.log('---- ' + t.passed + '/' + t.total + ' passed ----');
    process.exit(t.failed > 0 ? 1 : 0);
  }
}
// ===== RULES-ENGINE-END =====
