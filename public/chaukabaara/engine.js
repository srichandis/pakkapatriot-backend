// ===== RULES-ENGINE-START =====
/* Pure game rules for Chaukabaara (Chowka Bara / Ashta Chamma).
   No DOM dependencies — testable in Node. */
const ChaukRules = (function () {
  'use strict';

  const OUTER5 = [[0,2],[0,1],[1,0],[2,0],[3,0],[4,1],[4,2],[4,3],[3,4],[2,4],[1,4],[0,3]];
  const INNER5 = [[1,3],[2,3],[3,3],[3,2],[3,1],[2,1],[1,1],[1,2]];
  const OUTER7 = [[0,3],[0,2],[0,1],[0,0],[1,0],[2,0],[3,0],[4,0],[5,0],[6,0],[6,1],[6,2],[6,3],[6,4],[6,5],[6,6],[5,6],[4,6],[3,6],[2,6],[1,6],[0,6],[0,5],[0,4]];
  const INNER7 = [[1,4],[1,5],[2,5],[3,5],[4,5],[5,5],[5,4],[5,3],[5,2],[5,1],[4,1],[3,1],[2,1],[1,1],[1,2],[1,3],[2,3],[2,4],[3,4],[4,4],[4,3],[4,2],[3,2],[2,2]];

  function cellKey(pt) { return pt[0] + ',' + pt[1]; }
  function rotateCW(pt, n) { return [pt[1], n - 1 - pt[0]]; }
  function rotatePath(path, times, n) {
    let p = path.map(pt => pt.slice());
    for (let t = 0; t < times; t++) p = p.map(pt => rotateCW(pt, n));
    return p;
  }

  function buildGeometry(n) {
    const c = (n - 1) / 2;
    const outer = n === 5 ? OUTER5 : OUTER7;
    const inner = n === 5 ? INNER5 : INNER7;
    const topPath = outer.concat(inner).concat([[c, c]]);
    const outerLen = outer.length;
    const totalLen = topPath.length; // 21 for 5x5, 49 for 7x7
    const paths = [];
    for (let p = 0; p < 4; p++) paths.push(rotatePath(topPath, p, n));

    const safe = new Set(paths.map(pp => cellKey(pp[0])));
    safe.add(cellKey([c, c]));
    if (n === 7) {
      // X-marked safe squares on the classic wooden artwork: the four corners,
      // the four inner diagonals and the centre. Homes (start tips) stay safe.
      for (const pt of [[0,0],[0,6],[1,1],[1,5],[6,0],[6,6],[5,1],[5,5]]) safe.add(cellKey(pt));
    }
    // Full square board: every cell of the n×n grid is rendered (like the
    // classic Ashta Chamma diagrams). For 7x7 the four corners are part of
    // the outer ring path — they are the X-marked safe squares on the
    // classic wooden board, so pieces can land on them.
    const cells = [];
    for (let r = 0; r < n; r++) for (let cc = 0; cc < n; cc++) cells.push([r, cc]);
    return { n, c, outerLen, totalLen, paths, safe, cells };
  }

  function roll() {
    const mouths = [0, 1, 2, 3].map(() => Math.random() < 0.5);
    return rollFromMouths(mouths);
  }
  function rollFromMouths(mouths) {
    const up = mouths.filter(Boolean).length;
    const value = up === 0 ? 8 : up;
    return {
      mouths, up, value,
      bonus: value === 4 || value === 8,
      label: value === 4 ? 'Chamma' : value === 8 ? 'Ashta' : String(value),
      chamma: value === 4, ashta: value === 8
    };
  }

  /* Which physical arms (0=top,1=right,2=bottom,3=left) each player occupies */
  function armsFor(playerCount) {
    if (playerCount === 2) return [0, 2];      // opposite arms
    if (playerCount === 3) return [0, 1, 2];   // one arm rests
    return [0, 1, 2, 3];
  }
  function createGame({ n, playerCount, names, opts }) {
    const g = buildGeometry(n);
    const pieceCount = n - 1;
    const arms = armsFor(playerCount);
    const players = [];
    for (let i = 0; i < playerCount; i++) {
      players.push({
        idx: i,
        arm: arms[i],
        name: (names && names[i]) || 'Player ' + (i + 1),
        kills: 0,
        finished: 0,
        // Fruit-bowl variant: every pawn starts already on the board, stacked
        // on its home square, and travels the path to the centre (the fruit).
        pieces: Array.from({ length: pieceCount }, () => ({ status: 'on', pos: 0 }))
      });
    }
    return Object.assign({}, g, {
      playerCount, pieceCount, arms,
      opts: Object.assign({ entryAny: false }, opts),
      players, cur: 0, bonusCount: 0,
      blockState: new Map(),
      over: false, winner: -1
    });
  }

  /* All pieces (of any player) sitting on a given coordinate */
  function piecesAt(state, pt) {
    const key = cellKey(pt);
    const res = [];
    for (const pl of state.players) for (const pc of pl.pieces) {
      if (pc.status === 'on' && cellKey(state.paths[pl.arm][pc.pos]) === key) res.push({ player: pl, piece: pc });
    }
    return res;
  }
  function isSafe(state, pt) { return state.safe.has(cellKey(pt)); }

  /* Does moving from path-index k to d cross an armed opponent double? */
  /* Does moving from path-index k to d cross an armed opponent double?
     An armed double voids the crossing once per opponent — the block is
     consumed on that first attempt, matching "blocks the opponent for one move". */
  function crossesArmedDouble(state, playerIdx, k, d) {
    const pl = state.players[playerIdx];
    let wrap = false;
    let steps = d - k;
    if (k < state.outerLen && pl.kills === 0) {
      steps = (d - k + state.outerLen) % state.outerLen;
      wrap = true;
    }
    for (let i = 1; i <= steps; i++) {
      const idx = wrap ? (k + i) % state.outerLen : k + i;
      if (idx >= state.outerLen) break; // inner squares never block
      const pt = state.paths[pl.arm][idx];
      if (isSafe(state, pt)) continue;
      const b = state.blockState.get(cellKey(pt));
      if (b && !b.consumed.has(playerIdx)) {
        b.consumed.add(playerIdx); // the double has now blocked this opponent once
        return true;
      }
    }
    return false;
  }

  function legalTarget(state, pl, k, value) {
    let d;
    if (k < state.outerLen) {
      if (pl.kills > 0) d = k + value; else d = (k + value) % state.outerLen;
    } else {
      d = k + value;
    }
    if (d > state.totalLen - 1) return -1; // overshoots the centre
    if (d < 0) return -1;
    // landing on an opponent double is forbidden (a single can never hit a double)
    const landPt = state.paths[pl.arm][d];
    const opp = piecesAt(state, landPt).filter(o => o.player.idx !== pl.idx);
    if (opp.length >= 2 && !isSafe(state, landPt)) return -1;
    // crossing an armed double voids the move
    if (crossesArmedDouble(state, pl.idx, k, d)) return -1;
    return d;
  }

  function getLegalMoves(state, value) {
    const pl = state.players[state.cur];
    const moves = [];
    // All pawns are already on the board (fruit-bowl start) — no enter moves.
    for (let i = 0; i < pl.pieces.length; i++) {
      const pc = pl.pieces[i];
      if (pc.status !== 'on') continue;
      const d = legalTarget(state, pl, pc.pos, value);
      if (d === -1) continue;
      const landPt = state.paths[pl.arm][d];
      const capt = piecesAt(state, landPt).filter(o => o.player.idx !== pl.idx && !isSafe(state, landPt));
      const stack = piecesAt(state, state.paths[pl.arm][pc.pos]).filter(o => o.player.idx === pl.idx);
      moves.push({
        type: 'move', pieceIdx: i, from: pc.pos, to: d,
        stackSize: stack.length,
        captureCount: capt.length
      });
    }
    return moves;
  }

  /* Apply a move. move = {type:'move', pieces:[idx...], from, to} */
  function applyMove(state, move) {
    const pl = state.players[state.cur];
    const events = [];
    let captured = false;

    const landPt = state.paths[pl.arm][move.to];
    const idxs = move.pieces || [move.pieceIdx];
    let finished = 0;
    for (const idx of idxs) {
      const pc = pl.pieces[idx];
      if (pc.status !== 'on') continue;
      pc.pos = move.to;
      if (move.to === state.totalLen - 1) { pc.status = 'done'; pl.finished++; finished++; }
    }
    events.push({ type: 'move', count: idxs.length, to: move.to, finished });
    const capt = piecesAt(state, landPt).filter(o => o.player.idx !== pl.idx);
    if (capt.length > 0 && !isSafe(state, landPt)) {
      // A captured pawn returns to its home/cross square and stays on the board.
      for (const o of capt) { o.piece.status = 'on'; o.piece.pos = 0; }
      pl.kills += capt.length;
      captured = true;
      events.push({ type: 'capture', count: capt.length });
    }
    rebuildBlocks(state);
    return { events, captured };
  }

  /* Maintain the block map for opponent doubles on outer non-safe squares */
  function rebuildBlocks(state) {
    const counts = new Map(); // cellKey -> { owner, count }
    for (const pl of state.players) {
      for (const pc of pl.pieces) {
        if (pc.status !== 'on') continue;
        const pt = state.paths[pl.arm][pc.pos];
        if (pc.pos >= state.outerLen) continue; // inner squares never block
        const key = cellKey(pt);
        if (isSafe(state, pt)) continue;
        const e = counts.get(key) || { owner: pl.idx, count: 0 };
        e.count++;
        counts.set(key, e);
      }
    }
    const next = new Map();
    for (const [key, e] of counts) {
      if (e.count >= 2) {
        const prev = state.blockState.get(key);
        next.set(key, (prev && prev.owner === e.owner) ? prev : { owner: e.owner, consumed: new Set() });
      }
    }
    state.blockState = next;
  }

  function checkWin(state) {
    const pl = state.players[state.cur];
    if (pl.finished === state.pieceCount) { state.over = true; state.winner = state.cur; return true; }
    return false;
  }

  /* ---- snapshot for the three-consecutive-bonus forfeit ---- */
  function snapshot(state) {
    return {
      players: state.players.map(pl => ({
        idx: pl.idx, name: pl.name, kills: pl.kills, finished: pl.finished,
        pieces: pl.pieces.map(p => ({ status: p.status, pos: p.pos }))
      })),
      cur: state.cur, bonusCount: state.bonusCount,
      blockState: new Map([...state.blockState].map(([k, v]) => [k, { owner: v.owner, consumed: new Set(v.consumed) }])),
      over: state.over, winner: state.winner
    };
  }
  function restore(state, snap) {
    snap.players.forEach((sp, i) => {
      const pl = state.players[i];
      pl.kills = sp.kills; pl.finished = sp.finished;
      sp.pieces.forEach((pp, j) => { pl.pieces[j].status = pp.status; pl.pieces[j].pos = pp.pos; });
    });
    state.cur = snap.cur; state.bonusCount = snap.bonusCount;
    state.blockState = new Map([...snap.blockState].map(([k, v]) => [k, { owner: v.owner, consumed: new Set(v.consumed) }]));
    state.over = snap.over; state.winner = snap.winner;
  }

  /* ---- self tests ---- */
  function runSelfTests() {
    const results = [];
    const ok = (name, cond) => results.push({ name, pass: !!cond });
    const eq = (name, a, b) => results.push({ name, pass: JSON.stringify(a) === JSON.stringify(b), a, b });

    let g = buildGeometry(5);
    ok('5x5 outerLen=12', g.outerLen === 12);
    ok('5x5 totalLen=21', g.totalLen === 21);
    eq('5x5 centre', g.paths[0][20], [2, 2]);
    eq('5x5 homes', g.paths.map(p => p[0]), [[0,2],[2,4],[4,2],[2,0]]);
    ok('5x5 path distinct', new Set(g.paths[0].map(cellKey)).size === 21);
    eq('5x5 rotation', JSON.stringify(g.paths[1]), JSON.stringify(rotatePath(g.paths[0], 1, 5)));
    ok('5x5 inner clockwise from (1,3)', g.paths[0][12][0] === 1 && g.paths[0][12][1] === 3);

    g = buildGeometry(7);
    ok('7x7 outerLen=24', g.outerLen === 24);
    ok('7x7 totalLen=49', g.totalLen === 49);
    eq('7x7 centre', g.paths[0][48], [3, 3]);
    eq('7x7 homes', g.paths.map(p => p[0]), [[0,3],[3,6],[6,3],[3,0]]);
    ok('7x7 path distinct', new Set(g.paths[0].map(cellKey)).size === 49);
    eq('7x7 rotation', JSON.stringify(g.paths[1]), JSON.stringify(rotatePath(g.paths[0], 1, 7)));
    ok('7x7 corners on path', [[0,0],[0,6],[6,0],[6,6]].every(pt => g.paths[0].some(p => p[0] === pt[0] && p[1] === pt[1])));
    ok('7x7 cross cells safe', g.safe.has('0,0') && g.safe.has('6,6') && g.safe.has('1,1') && g.safe.has('5,5') && g.safe.has('3,3'));

    // dice values
    eq('roll 0 mouths -> 8', rollFromMouths([0,0,0,0]).value, 8);
    eq('roll 1 mouth -> 1', rollFromMouths([1,0,0,0]).value, 1);
    eq('roll 2 mouths -> 2', rollFromMouths([1,1,0,0]).value, 2);
    eq('roll 3 mouths -> 3', rollFromMouths([1,1,1,0]).value, 3);
    eq('roll 4 mouths -> 4', rollFromMouths([1,1,1,1]).value, 4);
    ok('8 is bonus', rollFromMouths([0,0,0,0]).bonus);
    ok('4 is bonus', rollFromMouths([1,1,1,1]).bonus);
    ok('3 not bonus', !rollFromMouths([1,1,1,0]).bonus);

    // fruit-bowl start: every pawn is on the board at home; no enter moves
    let s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: {} });
    ok('all pieces on board at home', s.players[0].pieces.every(p => p.status === 'on' && p.pos === 0));
    ok('no enter moves on 1', !getLegalMoves(s, 1).some(m => m.type === 'enter'));
    ok('no enter moves on 4', !getLegalMoves(s, 4).some(m => m.type === 'enter'));
    ok('move on 1', getLegalMoves(s, 1).some(m => m.type === 'move'));

    // movement
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[0].pieces[0] = { status: 'on', pos: 5 };
    ok('move 5->8 on 3', getLegalMoves(s, 3).some(m => m.type === 'move' && m.to === 8));

    // wrap on outer without kills
    s.players[0].pieces[0] = { status: 'on', pos: 10 };
    ok('wrap 10+4 -> 2 (no kills)', getLegalMoves(s, 4).some(m => m.to === 2));
    // with kills, enter inner
    s.players[0].kills = 1;
    ok('inner 10+4 -> 14 (kills)', getLegalMoves(s, 4).some(m => m.to === 14));
    // exact count to centre
    s.players[0].pieces[0] = { status: 'on', pos: 19 };
    // note: other pieces stay at pos 0 (fruit-bowl start), so only piece 0 is checked here
    ok('overshoot centre illegal', !getLegalMoves(s, 4).some(m => m.type === 'move' && m.pieceIdx === 0));
    ok('exact 19+1 -> 20 centre', getLegalMoves(s, 1).some(m => m.type === 'move' && m.pieceIdx === 0 && m.to === 20));

    // 2P / 3P arm assignment
    ok('2P opposite arms', JSON.stringify(armsFor(2)) === '[0,2]');
    ok('3P arms', JSON.stringify(armsFor(3)) === '[0,1,2]');
    let s2 = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    ok('2P players use opposite arms', JSON.stringify(s2.players.map(p => p.arm)) === '[0,2]');
    ok('2P B home is bottom tip', JSON.stringify(s2.paths[s2.players[1].arm][0]) === '[4,2]');

    // capture: A piece at 5 lands on B piece whose coordinate == pathA[8]
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[0].pieces[0] = { status: 'on', pos: 5 };
    const targetPt = s.paths[0][8];
    const bIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(targetPt));
    ok('found B index for capture', bIdx !== -1);
    s.players[1].pieces[0] = { status: 'on', pos: bIdx };
    const moves = getLegalMoves(s, 3);
    ok('capture move available', moves.some(m => m.type === 'move' && m.to === 8 && m.captureCount === 1));
    const r = applyMove(s, { type: 'move', pieces: [0], from: 5, to: 8 });
    ok('capture happened', r.captured);
    ok('B piece returns to its cross square', s.players[1].pieces[0].status === 'on' && s.players[1].pieces[0].pos === 0);
    ok('A kills incremented', s.players[0].kills === 1);

    // safe square: B piece on B home (safe) not captured
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[1].pieces[0] = { status: 'on', pos: 0 }; // B home = (4,2), safe
    const homePt = s.paths[s.players[1].arm][0];
    const aIdx = s.paths[0].findIndex(pt => cellKey(pt) === cellKey(homePt));
    ok('A index reaching B home', aIdx === 6);
    s.players[0].pieces[0] = { status: 'on', pos: 3 };
    const m2 = getLegalMoves(s, 3); // 3->6 lands on B home
    ok('no capture on safe square', m2.some(m => m.to === 6 && m.captureCount === 0));

    // double blocking: B has a double on an outer cell; A's first crossing is voided & consumed
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    const blockCellIdx = 4; // A-path index 4 = (3,0), outer non-safe
    const blockPt = s.paths[0][blockCellIdx];
    const bBlockIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(blockPt));
    s.players[1].pieces[0] = { status: 'on', pos: bBlockIdx };
    s.players[1].pieces[1] = { status: 'on', pos: bBlockIdx };
    rebuildBlocks(s);
    ok('double armed', s.blockState.has(cellKey(blockPt)));
    s.players[0].pieces[0] = { status: 'on', pos: 2 };
    ok('crossing double illegal', !getLegalMoves(s, 3).some(m => m.type === 'move' && m.to === 5));
    ok('short move legal', getLegalMoves(s, 1).some(m => m.to === 3));
    ok('crossing legal after block consumed', getLegalMoves(s, 3).some(m => m.type === 'move' && m.to === 5));

    // landing on an opponent double is always illegal (and never consumes the block)
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    const lIdx = 5; const lPt = s.paths[0][lIdx]; // (4,1), outer non-safe
    const bLIdx = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(lPt));
    s.players[1].pieces[0] = { status: 'on', pos: bLIdx };
    s.players[1].pieces[1] = { status: 'on', pos: bLIdx };
    s.players[0].pieces[0] = { status: 'on', pos: 2 };
    rebuildBlocks(s);
    ok('landing on double illegal', !getLegalMoves(s, 3).some(m => m.type === 'move' && m.to === 5));
    ok('landing block not consumed', s.blockState.get(cellKey(lPt)).consumed.size === 0);

    // inner squares never block
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[0].kills = 1;
    const iIdx = 13; const iPt = s.paths[0][iIdx];
    const bIIndex = s.paths[s.players[1].arm].findIndex(pt => cellKey(pt) === cellKey(iPt));
    s.players[1].pieces[0] = { status: 'on', pos: bIIndex };
    s.players[1].pieces[1] = { status: 'on', pos: bIIndex };
    s.players[0].pieces[0] = { status: 'on', pos: 12 };
    rebuildBlocks(s);
    ok('no block entries in inner', !s.blockState.has(cellKey(iPt)));
    ok('crossing inner double legal', getLegalMoves(s, 2).some(m => m.to === 14));

    // snapshot / restore
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[0].pieces[0] = { status: 'on', pos: 5 };
    s.players[0].kills = 2;
    const snap = snapshot(s);
    s.players[0].pieces[0] = { status: 'off', pos: -1 };
    s.players[0].kills = 9;
    restore(s, snap);
    ok('restore pieces', s.players[0].pieces[0].status === 'on' && s.players[0].pieces[0].pos === 5);
    ok('restore kills', s.players[0].kills === 2);

    // 7x7 basic play
    s = createGame({ n: 7, playerCount: 4, names: ['A','B','C','D'], opts: { entryAny: false } });
    ok('7x7 pieceCount 6', s.pieceCount === 6);
    ok('7x7 no enter moves', !getLegalMoves(s, 4).some(m => m.type === 'enter'));
    s.players[0].pieces[0] = { status: 'on', pos: 5 };
    ok('7x7 move', getLegalMoves(s, 2).some(m => m.to === 7));
    s.players[0].kills = 1;
    s.players[0].pieces[0] = { status: 'on', pos: 23 };
    ok('7x7 enter inner 23+1 -> 24', getLegalMoves(s, 1).some(m => m.to === 24));
    s.players[0].pieces[0] = { status: 'on', pos: 46 };
    ok('7x7 overshoot illegal', !getLegalMoves(s, 8).some(m => m.type === 'move' && m.to > 48));
    s.players[0].pieces[0] = { status: 'on', pos: 46 };
    ok('7x7 exact to centre', getLegalMoves(s, 2).some(m => m.to === 48));

    // win detection
    s = createGame({ n: 5, playerCount: 2, names: ['A', 'B'], opts: { entryAny: false } });
    s.players[0].finished = 4;
    ok('win detected', checkWin(s) && s.winner === 0);

    const failed = results.filter(r => !r.pass);
    const lines = results.map(r => (r.pass ? 'PASS' : 'FAIL') + '  ' + r.name + (r.pass ? '' : '  got=' + JSON.stringify(r.a) + ' want=' + JSON.stringify(r.b)));
    return { total: results.length, passed: results.length - failed.length, failed: failed.length, lines };
  }

  return {
    buildGeometry, armsFor, roll, rollFromMouths, createGame,
    getLegalMoves, applyMove, checkWin, snapshot, restore,
    rebuildBlocks, piecesAt, isSafe, crossesArmedDouble,
    cellKey, runSelfTests
  };
})();

if (typeof module !== 'undefined' && module.exports) {
  module.exports = ChaukRules;
  if (require.main === module) {
    const t = ChaukRules.runSelfTests();
    console.log(t.lines.join('\n'));
    console.log('---- ' + t.passed + '/' + t.total + ' passed ----');
    process.exit(t.failed > 0 ? 1 : 0);
  }
}
// ===== RULES-ENGINE-END =====
