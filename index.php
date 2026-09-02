<?php
require 'db.php'; require 'auth.php'; require_login();
$userId=(int)$_SESSION['user_id'];
$q=$pdo->query('SELECT s.*, d.diagnostic_type,d.diagnostic_result FROM scenarios s LEFT JOIN diagnostics d ON d.scenario_id=s.scenario_id ORDER BY s.scenario_id');
$sc=[]; foreach($q as $r){$id=(int)$r['scenario_id']; if(!isset($sc[$id])){$sc[$id]=['id'=>$id,'title'=>$r['title'],'desc'=>$r['description'],'issue_type'=>$r['issue_type'],'difficulty'=>(int)$r['difficulty'],'correctFix'=>$r['correct_fix']];}$sc[$id][$r['diagnostic_type']]=$r['diagnostic_result'];}
$scenarios=array_values($sc);
$u=$pdo->prepare('SELECT total_xp FROM users WHERE user_id=?');$u->execute([$userId]);$totalXp=(int)$u->fetchColumn();
$done=$pdo->prepare('SELECT scenario_id FROM user_scenario_progress WHERE user_id=? AND completed_at IS NOT NULL');$done->execute([$userId]);$completed=array_map('intval',$done->fetchAll(PDO::FETCH_COLUMN));
$badges=$pdo->prepare('SELECT a.scenario_id,a.achievement_name,a.description,(ua.user_achievement_id IS NOT NULL) unlocked FROM achievements a LEFT JOIN user_achievements ua ON ua.achievement_id=a.achievement_id AND ua.user_id=? ORDER BY a.scenario_id');$badges->execute([$userId]);$badgeRows=$badges->fetchAll();
$leaders=$pdo->query('SELECT full_name,total_xp FROM users ORDER BY total_xp DESC, full_name ASC LIMIT 5')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SmartFix-AI</title><link rel="stylesheet" href="style.css?v=2"><style>
.scenario-instruction{margin:14px 0;padding:14px 16px;border:2px solid #ef4444;border-radius:10px;background:rgba(127,29,29,.28);color:#fecaca;font-weight:700;line-height:1.55;box-shadow:0 0 14px rgba(239,68,68,.18)}
.scenario-instruction strong{display:block;margin-bottom:4px;color:#ff4d4d;letter-spacing:.04em}
.scenario-instruction.resolved{border-color:#22c55e;background:rgba(20,83,45,.32);color:#dcfce7;box-shadow:0 0 16px rgba(34,197,94,.22)}
.scenario-instruction.resolved strong{color:#4ade80}
.scenario-visual{margin:20px auto;max-width:820px;border:1px solid #334155;border-radius:14px;overflow:hidden;background:#071626;box-shadow:0 14px 35px rgba(0,0,0,.3)}
.scenario-visual img{display:block;width:100%;height:auto}
.scenario-visual figcaption{padding:9px 12px;color:#b9c9dc;font-size:.82rem;text-align:center}

/* Projector-friendly light theme */
html,body{background:#edf4f8!important;color:#102a43!important}
body{color-scheme:light}
.container{background:transparent!important}
.topbar,.panel{background:#fff!important;border-color:#b8c9d9!important;color:#102a43!important;box-shadow:0 8px 24px rgba(15,42,67,.10)!important}
.topbar{border-bottom:4px solid #0ea5e9!important}
.topbar h1,.panel h2,.panel h3{color:#0b4f6c!important}
.topbar a{color:#0369a1!important;font-weight:700}
.muted{color:#52677b!important}
#case-number,.case-head .muted,#score-value{color:#fbbf24!important;text-shadow:0 1px 1px rgba(0,0,0,.18)}
#timer{color:#ef4444!important;text-shadow:0 1px 1px rgba(0,0,0,.18)}
.leaderboard th{color:#0b4f6c!important;background:#e7f3fa!important}
.leaderboard td,.leaderboard th{border-color:#c7d5e2!important}
.scenario-btn{background:#f6fafe!important;color:#102a43!important;border:2px solid #c4d3df!important;box-shadow:0 3px 9px rgba(15,42,67,.06)!important}
.scenario-btn:hover{background:#eaf6fc!important;border-color:#38bdf8!important}
.scenario-btn.active{background:#dff3ff!important;color:#082f49!important;border-color:#0284c7!important;box-shadow:0 0 0 3px rgba(14,165,233,.18)!important}
.scenario-summary{color:#40566b!important}
.scenario-check{color:#047857!important}
.metric,.achievement,.btn-action,.secondary{background:#f7fafc!important;color:#102a43!important;border-color:#b8c9d9!important}
.metric{box-shadow:inset 0 0 0 1px rgba(148,163,184,.12)!important}
.btn-action{font-weight:700!important;box-shadow:0 2px 6px rgba(15,42,67,.08)!important}
.btn-action:hover,.secondary:hover{background:#dff3ff!important;border-color:#0284c7!important;color:#082f49!important}
.achievement{opacity:1!important}
.achievement.unlocked{background:#e7f8ee!important;border-color:#22a06b!important}
.achievement strong{color:#183b56!important}
.badge{background:#e0f2fe!important;color:#075985!important;border-color:#38bdf8!important}
.badge.unresolved{background:#fee2e2!important;color:#991b1b!important;border-color:#dc2626!important}
.badge.resolved{background:#dcfce7!important;color:#166534!important;border-color:#16a34a!important}
.scenario-instruction{background:#fee8e8!important;color:#7f1d1d!important;border-color:#dc2626!important;box-shadow:0 0 0 3px rgba(220,38,38,.09)!important}
.scenario-instruction strong{color:#b91c1c!important}
.scenario-instruction.resolved{background:#dcfce7!important;color:#14532d!important;border-color:#16a34a!important;box-shadow:0 0 0 3px rgba(22,163,74,.10)!important}
.scenario-instruction.resolved strong{color:#15803d!important}
.scenario-visual{background:#fff!important;border-color:#aebfce!important;box-shadow:0 10px 24px rgba(15,42,67,.12)!important}
.scenario-visual figcaption{background:#f3f8fc!important;color:#334e68!important}
.console{background:#0d2b3e!important;color:#d9f7ff!important;border-color:#26708f!important;box-shadow:inset 0 0 0 1px rgba(103,232,249,.12)!important}
#sfPC .part-card{background:#f7fafc!important;border-color:#b8c9d9!important;color:#102a43!important}
#sfPC small,#sfPC .pc-note{color:#40566b!important}
#sfPC .hotspot{background:#092f46!important;color:#fff!important;border-color:#22b8e6!important}
#sfPC .hotspot.fault{background:#991b1b!important;border-color:#ef4444!important;color:#fff!important}
#sfPC .hotspot.resolved{background:#166534!important;border-color:#22c55e!important;color:#fff!important}
.language-switch{display:inline-flex;gap:4px;margin-bottom:8px;padding:3px;border:1px solid #9fb3c5;border-radius:9px;background:#eaf2f8}
.language-switch button{min-width:44px;padding:6px 10px;border:0;border-radius:6px;background:transparent;color:#334e68;font-weight:800;cursor:pointer}
.language-switch button.active{background:#0284c7;color:#fff;box-shadow:0 2px 8px rgba(2,132,199,.28)}
.language-switch button:focus-visible{outline:3px solid #fbbf24;outline-offset:2px}
@media (max-width:800px){.topbar{gap:12px}.panel{box-shadow:0 5px 16px rgba(15,42,67,.08)!important}}
</style></head><body>
<div class="container"><header class="topbar"><div><h1>SMARTFIX</h1><div class="muted">INTERACTIVE COMPUTER TROUBLESHOOTING SIMULATOR</div></div><div style="text-align:right"><div class="language-switch" aria-label="Language selection"><button type="button" id="lang-bm" aria-pressed="false">BM</button><button type="button" id="lang-en" aria-pressed="true">EN</button></div><br><strong><?=htmlspecialchars($_SESSION['full_name'])?></strong><div><span id="header-xp"><?=$totalXp?> XP</span> · <a href="logout.php" style="color:#38bdf8">Logout</a></div></div></header>
<aside><div class="panel"><h2>1. Select Scenario</h2><div id="scenario-list"></div></div><div class="panel" style="margin-top:20px"><h2>🏆 Leaderboard</h2><table class="leaderboard"><tr><th>Name</th><th>XP</th></tr><?php foreach($leaders as $l):?><tr><td><?=htmlspecialchars($l['full_name'])?></td><td><?=$l['total_xp']?></td></tr><?php endforeach;?></table></div></aside>
<main class="panel"><h2>2. Diagnostics & Repair Workspace</h2><div class="case-head"><div><h3 id="case-number">CASE #--</h3><span id="issue-type" class="badge">SELECT CASE</span><div id="difficulty" style="color:#fbbf24;margin-top:5px">☆☆☆☆☆</div></div><div style="text-align:right"><div class="muted">TOTAL SCORE</div><strong id="score-value" style="font-size:1.6rem;color:#38bdf8"><?=$totalXp?> XP</strong><div class="muted">TIME REMAINING</div><strong id="timer" style="font-size:1.4rem;color:#ef4444">04:59</strong></div></div>
<h3 id="scenario-title">Select a scenario to start</h3><p id="scenario-desc" class="muted"></p><div id="scenario-instruction" class="scenario-instruction" hidden></div><div id="scenario-status"></div>
<div class="grid-2"><div class="metric"><div class="muted">SYSTEM STATE</div><strong id="metric-state">--</strong></div><div class="metric"><div class="muted">DIAGNOSTIC READING</div><strong id="metric-reading">--</strong></div></div>
<figure id="scenario-visual" class="scenario-visual" hidden><img id="scenario-visual-img" src="" alt=""><figcaption id="scenario-visual-caption"></figcaption></figure>
<div class="pc-wrapper"><img src="assets/pc_main.png" alt="SmartFix PC"><span id="cpu-part" class="hotspot" style="top:29%;left:50%">CPU</span><span id="ram-part" class="hotspot" style="top:32%;left:63%">RAM</span><span id="gpu-part" class="hotspot" style="top:58%;left:49%">GPU</span><span id="mb-part" class="hotspot" style="top:47%;left:50%">MB</span><span id="storage-part" class="hotspot" style="top:68%;left:77%">SSD</span><span id="network-part" class="hotspot" style="top:40%;left:84%">LAN</span><span id="usb-part" class="hotspot" style="top:30%;left:61%">USB</span><span id="audio-part" class="hotspot" style="top:40%;left:61%">AUDIO</span></div>
<div id="console" class="console">&gt; SmartFix Ready. Choose a scenario to begin.</div>
<h3>Step A: Run Diagnostic Tools</h3><div class="grid-2"><button class="btn-action" onclick="runDiagnostic('beep')">🔊 Check Beep Codes</button><button class="btn-action" onclick="runDiagnostic('thermal')">🌡️ Thermal / Hardware Sensor</button><button class="btn-action" onclick="runDiagnostic('network')">🌐 IP / Network Status</button><button class="btn-action" onclick="runDiagnostic('boot')">💽 BIOS Boot Priority</button><button class="btn-action" onclick="runDiagnostic('startup')">📊 Check Startup Applications</button><button class="btn-action" onclick="runDiagnostic('driver')">🖥️ Check System Error / Driver</button><button class="btn-action" onclick="runDiagnostic('usb')">🔌 Check USB Device Status</button><button class="btn-action" onclick="runDiagnostic('audio')">🔊 Check Audio Output</button></div>
<h3>Step B: Apply Fix Action</h3><div class="grid-2"><button class="btn-action" onclick="applyFix('ram')">🔧 Reseat RAM Module</button><button class="btn-action" onclick="applyFix('paste')">🔧 Re-apply Thermal Paste</button><button class="btn-action" onclick="applyFix('ip')">🔧 Renew IP (DHCP)</button><button class="btn-action" onclick="applyFix('bios')">🔧 Reset BIOS Boot Order</button><button class="btn-action" onclick="applyFix('startup')">🚀 Disable Unnecessary Startup Apps</button><button class="btn-action" onclick="applyFix('driver')">🧩 Roll Back Faulty Display Driver</button><button class="btn-action" onclick="applyFix('usb')">🔌 Reinstall USB Device Driver</button><button class="btn-action" onclick="applyFix('audio')">🔊 Select Speakers as Default Output</button></div><button class="btn secondary" style="width:100%;margin-top:10px" onclick="resetCase()">🔄 Reset Current Case</button>
<h2 style="margin-top:25px">🏅 Achievement Center</h2><div class="achievements"><?php foreach($badgeRows as $b):
  $achievementDescription = ((int)$b['scenario_id'] >= 5 && (int)$b['scenario_id'] <= 8)
    ? 'Solve Case #' . (int)$b['scenario_id']
    : $b['description'];
?><div class="achievement <?=$b['unlocked']?'unlocked':''?>" id="badge-<?=$b['scenario_id']?>"><div style="font-size:2rem"><?=$b['unlocked']?'🏅':'🔒'?></div><strong><?=htmlspecialchars($b['achievement_name'])?></strong><div class="muted"><?=htmlspecialchars($achievementDescription)?></div></div><?php endforeach;?></div></main></div>
<script>
const scenarios=<?=json_encode($scenarios,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>; const completed=new Set(<?=json_encode($completed)?>); let current=null,timeLeft=299,timerInterval=null,resolved=false;
function log(m){document.getElementById('console').innerText=m} function init(){const list=document.getElementById('scenario-list');list.innerHTML='';scenarios.forEach(s=>{const b=document.createElement('button');b.className='scenario-btn '+(current&&current.id===s.id?'active':'');b.textContent=s.title+(completed.has(s.id)?' ✓':'');b.onclick=()=>selectScenario(s);list.appendChild(b)})}
function resetHighlights(){document.querySelectorAll('.hotspot').forEach(e=>e.classList.remove('fault','resolved'))} function faultFor(s){return s.correctFix==='startup'?null:s.correctFix==='driver'?'gpu':s.correctFix==='usb'?'usb':s.correctFix==='audio'?'audio':s.correctFix==='paste'?'cpu':s.correctFix==='ip'?'network':s.correctFix==='bios'?'storage':'ram'} function setPart(name,cls){const part=name&&document.getElementById(name+'-part');if(part)part.classList.add(cls)}
function log(m) {
  document.getElementById('console').innerText = m;
}

const scenarioIcons = {
  ram: {
    color: '#a970ff',
    svg: `<rect x="3" y="4" width="26" height="18" rx="2"/>
          <path d="M12 28h8M16 22v6M10 8l5 5-2 2 7 5"/>`
  },
  paste: {
    color: '#ff5252',
    svg: `<path d="M13 19V7a3 3 0 0 1 6 0v12a6 6 0 1 1-6 0Z"/>
          <path d="M16 11v12M23 9h3M23 14h3"/>
          <circle cx="16" cy="24" r="2"/>`
  },
  ip: {
    color: '#22ef9a',
    svg: `<path d="M3 11a20 20 0 0 1 26 0
                  M7 16a14 14 0 0 1 18 0
                  M11 21a8 8 0 0 1 10 0"/>
          <circle cx="16" cy="27" r="1" fill="currentColor"/>`
  },
  bios: {
    color: '#ffce32',
    svg: `<rect x="6" y="3" width="20" height="26" rx="3"/>
          <circle cx="16" cy="11" r="4"/>
          <path d="M7 21h18M11 25h1M16 25h5"/>`
  },
  startup: {
    color: '#38bdf8',
    svg: `<rect x="3" y="5" width="26" height="19" rx="2"/>
          <path d="M8 19l5-5 4 3 7-8M11 28h10M16 24v4"/>`
  },
  driver: {
    color: '#3b82f6',
    svg: `<rect x="3" y="5" width="26" height="19" rx="2"/>
          <path d="M9 11l4 4-4 4M16 19h7M11 28h10M16 24v4"/>`
  },
  usb: {
    color: '#f59e0b',
    svg: `<path d="M16 3v21M16 3l-3 4M16 3l3 4M16 12l7-4M23 8v5M16 17l-7-4M9 13v4"/>
          <circle cx="9" cy="19" r="2"/><rect x="21" y="13" width="4" height="4"/>`
  },
  audio: {
    color: '#f43f5e',
    svg: `<path d="M5 13h6l6-5v16l-6-5H5zM21 12l7 8M28 12l-7 8"/>`
  }
};

const scenarioSupport = {
  ram: {
    instruction: 'Run Check Beep Codes, observe the diagnostic result, identify the RAM fault, and choose the correct repair action.'
  },
  paste: {
    instruction: 'Run Thermal / Hardware Sensor, observe the CPU temperature, and choose the correct repair action.'
  },
  ip: {
    instruction: 'Run IP / Network Status, inspect the network configuration, and choose the correct repair action.'
  },
  bios: {
    instruction: 'Run BIOS Boot Priority, inspect the detected boot device and boot order, and choose the correct repair action.'
  },
  startup: {
    instruction: 'Run Check Startup Applications, identify the unnecessary high-impact applications, and choose the correct repair action.',
    image: 'assets/scenario5-startup-apps.png',
    alt: 'Startup Apps manager showing several enabled applications with high startup impact.',
    caption: 'Scenario 5 visual: unnecessary startup applications are slowing down the computer.'
  },
  driver: {
    instruction: 'Run Check System Error / Driver, observe the diagnostic result, and choose the most appropriate repair action.',
    image: 'assets/scenario6-blue-screen.png',
    alt: 'Desktop computer displaying a blue system error and restart indicator.',
    caption: 'Scenario 6 visual: blue-screen system crash and unexpected restart.'
  },
  usb: {
    instruction: 'Run Check USB Device Status, identify why the flash drive is not detected, and choose the correct repair action.',
    image: 'assets/scenario7-usb-not-detected.png',
    alt: 'USB flash drive connected to a computer with a device-not-detected warning.',
    caption: 'Scenario 7 visual: connected USB device is not detected.'
  },
  audio: {
    instruction: 'Run Check Audio Output, inspect the selected output device, and choose the correct repair action.',
    image: 'assets/scenario8-no-sound.png',
    alt: 'Desktop speakers and monitor displaying a muted-audio warning.',
    caption: 'Scenario 8 visual: audio is playing but no sound is heard.'
  }
};

function renderScenarioSupport(s) {
  const support = scenarioSupport[s.correctFix];
  const instruction = document.getElementById('scenario-instruction');
  const visual = document.getElementById('scenario-visual');
  const image = document.getElementById('scenario-visual-img');
  const caption = document.getElementById('scenario-visual-caption');
  const pc = document.querySelector('.pc-wrapper');

  if (!support) {
    instruction.hidden = true;
    visual.hidden = true;
    if (pc) {
      pc.hidden = false;
      pc.style.display = '';
    }
    return;
  }

  instruction.innerHTML = '<strong>SCENARIO INSTRUCTION</strong>' + support.instruction;
  instruction.hidden = false;

  if (support.image) {
    image.src = support.image;
    image.alt = support.alt;
    caption.textContent = support.caption;
    visual.hidden = false;
  } else {
    image.removeAttribute('src');
    image.alt = '';
    caption.textContent = '';
    visual.hidden = true;
  }

  if (pc) {
    pc.hidden = false;
    pc.style.display = '';
  }
}

function setInstructionState(isResolved) {
  const instruction = document.getElementById('scenario-instruction');
  const support = current && scenarioSupport[current.correctFix];

  if (!instruction || !support) return;

  instruction.classList.toggle('resolved', isResolved);
  instruction.innerHTML = isResolved
    ? '<strong>SCENARIO COMPLETED ✓</strong>Correct repair action applied successfully. The system has been restored.'
    : '<strong>SCENARIO INSTRUCTION</strong>' + support.instruction;
  instruction.hidden = false;
}

function init() {
  const list = document.getElementById('scenario-list');
  list.innerHTML = '';

  scenarios.forEach(s => {
    const icon = scenarioIcons[s.correctFix] || scenarioIcons.ram;
    const selected = Boolean(current && current.id === s.id);
    const isCompleted = completed.has(s.id);

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'scenario-btn' + (selected ? ' active' : '');
    button.style.setProperty('--scenario-accent', icon.color);
    button.setAttribute('aria-pressed', String(selected));

    const iconBox = document.createElement('span');
    iconBox.className = 'scenario-icon';
    iconBox.setAttribute('aria-hidden', 'true');
    iconBox.innerHTML = `
      <svg viewBox="0 0 32 32" fill="none"
           stroke="currentColor" stroke-width="1.8"
           stroke-linecap="round" stroke-linejoin="round">
        ${icon.svg}
      </svg>
    `;

    const textBox = document.createElement('span');
    textBox.className = 'scenario-copy';

    const heading = document.createElement('span');
    heading.className = 'scenario-label';
    heading.textContent = 'Scenario ' + s.id;

    const description = document.createElement('span');
    description.className = 'scenario-summary';
    description.textContent = s.title.replace(
      /^scenario\s*\d+\s*[:.\-–—]?\s*/i,
      ''
    );

    textBox.append(heading, description);
    button.append(iconBox, textBox);

    if (isCompleted) {
      const check = document.createElement('span');
      check.className = 'scenario-check';
      check.textContent = '✓';
      check.setAttribute('aria-label', 'Completed');
      button.appendChild(check);
    }

    button.onclick = () => selectScenario(s);
    list.appendChild(button);
  });
} 
function startTimer() {
  clearInterval(timerInterval);
  timeLeft = 299;
  drawTime();

  timerInterval = setInterval(() => {
    timeLeft--;
    drawTime();

    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      log('> TIMER EXPIRED: Diagnostic session ended.');
    }
  }, 1000);
}

function drawTime() {
  const minutes = String(Math.floor(timeLeft / 60)).padStart(2, '0');
  const seconds = String(timeLeft % 60).padStart(2, '0');
  document.getElementById('timer').innerText = minutes + ':' + seconds;
}

function selectScenario(s) {
  current = s;
  resolved = false;

  resetHighlights();
  setPart(faultFor(s), 'fault');
  renderScenarioSupport(s);
  setInstructionState(false);

  document.getElementById('case-number').innerText = 'CASE #' + s.id;
  document.getElementById('issue-type').innerText = s.issue_type + ' ISSUE';
  document.getElementById('difficulty').innerText =
    '★'.repeat(s.difficulty) + '☆'.repeat(5 - s.difficulty);

  document.getElementById('scenario-title').innerText = s.title;
  document.getElementById('scenario-desc').innerText = s.desc;
  document.getElementById('scenario-status').innerHTML =
    '<span class="badge unresolved">FAULT UNRESOLVED</span>';

  document.getElementById('metric-state').innerText = 'FAULT ACTIVE';
  document.getElementById('metric-reading').innerText = 'Run Diagnostic';

  log('> Scenario Selected: ' + s.title +
      '\n> Ready for diagnostic testing.');

  startTimer();
  init();
}

function runDiagnostic(type) {
  if (!current) {
    log('> ERROR: Select a scenario first.');
    return;
  }

  const result = current[type] || 'No reading';
  document.getElementById('metric-reading').innerText = result;

  log('> [DIAGNOSTIC ' + type.toUpperCase() + ']\n  Result: ' + result);
}

async function applyFix(fixType) {
  if (!current) {
    log('> ERROR: Select a scenario first.');
    return;
  }

  if (resolved) {
    log('> SYSTEM NOTICE: This case is already resolved in this session.');
    return;
  }

  const response = await fetch('api_fix.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      scenario_id: current.id,
      fix_type: fixType,
      time_taken: 299 - timeLeft
    })
  });

  const data = await response.json();

  if (!data.ok) {
    log('> ERROR: ' + data.message);
    return;
  }

  if (data.correct) {
    resolved = true;
    clearInterval(timerInterval);

    resetHighlights();
    setPart(faultFor(current), 'resolved');
    completed.add(current.id);

    document.getElementById('scenario-status').innerHTML =
      '<span class="badge resolved">SYSTEM RESTORED</span>';

    document.getElementById('metric-state').innerText = 'OPERATIONAL';
    setInstructionState(true);
    document.getElementById('score-value').innerText = data.total_xp + ' XP';
    document.getElementById('header-xp').innerText = data.total_xp + ' XP';

    const badge = document.getElementById('badge-' + current.id);

    if (badge) {
      badge.classList.add('unlocked');
      badge.children[0].innerText = '🏅';
    }

    log('> [REPAIR SUCCESS]\n  ' + data.message +
        (data.xp_added
          ? '\n> +' + data.xp_added + ' XP earned.'
          : '\n> Case replayed: XP already awarded previously.'));

    init();
  } else {
    log('> [REPAIR FAILED]\n  ' + data.message);
  }
}

function resetCase() {
  if (!current) {
    log('> ERROR: Select a scenario first.');
    return;
  }

  resolved = false;
  resetHighlights();
  setPart(faultFor(current), 'fault');

  document.getElementById('scenario-status').innerHTML =
    '<span class="badge unresolved">FAULT UNRESOLVED</span>';

  document.getElementById('metric-state').innerText = 'FAULT ACTIVE';
  document.getElementById('metric-reading').innerText = 'Run Diagnostic';
  setInstructionState(false);

  startTimer();

  log('> CASE RESET SUCCESSFULLY\n> ' + current.title +
      '\n> Run diagnostics and repair again.');
}

init();
</script>
<script>
(() => {
  const box = document.querySelector('.pc-wrapper');
  if (!box || box.dataset.hardwareReady) return;

  const photo = box.querySelector('img');
  if (!photo) return;

  box.dataset.hardwareReady = 'true';
  box.id = 'sfPC';

  const style = document.createElement('style');
  style.textContent = `
    #sfPC {
      display:block;
      position:relative;
      width:100%;
      max-width:560px;
      height:auto;
      padding:0;
      margin:24px auto;
      border:0;
      background:transparent;
      box-sizing:border-box;
    }

    #sfPC .pc-scene {
      position:relative;
      width:100%;
    }

    #sfPC .pc-scene > img {
      display:block;
      position:static;
      width:100%;
      max-width:none;
      height:auto;
      margin:0;
      padding:0;
      border:0;
    }

    #sfPC .pc-lines {
      position:absolute;
      inset:0;
      width:100%;
      height:100%;
      pointer-events:none;
    }

    #sfPC .hotspot {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      position:absolute;
      top:auto;
      left:auto;
      right:auto;
      bottom:auto;
      width:auto;
      height:auto;
      padding:5px 8px;
      transform:translate(-50%,-50%);
      z-index:2;
      border:2px solid #38bdf8;
      border-radius:20px;
      background:#071626;
      color:white;
      font:700 11px/1.3 Arial,sans-serif;
      white-space:nowrap;
      animation:none;
      opacity:1;
    }

    #sfPC #cpu-part {
      left:27%;
      top:43%;
    }

    #sfPC #ram-part {
      left:69%;
      top:27%;
    }

    #sfPC .hidden-parts {
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:10px;
      margin-top:12px;
    }

    #sfPC .part-card {
      display:flex;
      align-items:center;
      gap:10px;
      padding:12px;
      background:#0b1423;
      border:1px solid #334155;
      border-radius:10px;
    }

    #sfPC .part-card .hotspot {
      position:static;
      transform:none;
      flex-shrink:0;
    }

    #sfPC small,
    #sfPC .pc-note {
      color:#b9c9dc;
      font:12px/1.5 Arial,sans-serif;
    }

    #sfPC .hotspot.fault {
      background:#4b1721;
      border-color:#fb7185;
      color:white;
    }

    #sfPC[data-active="true"][data-blink="true"] .hotspot.fault {
      animation:hardwarePulse 1.6s ease-in-out infinite;
    }

    #sfPC .hotspot.resolved {
      background:#083e32;
      border-color:#34d399;
      color:#d1fae5;
      animation:none;
    }

    #sfPC .hotspot.fault::after {
      content:' !';
      margin-left:3px;
    }

    #sfPC .hotspot.resolved::after {
      content:' ✓';
      margin-left:3px;
    }

    #sfPC .pc-controls {
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:12px;
    }

    #sfPC .pc-controls button {
      width:auto;
      padding:8px 12px;
      border:1px solid #42617c;
      border-radius:8px;
      background:#101e32;
      color:white;
      font:600 12px/1.4 Arial,sans-serif;
      cursor:pointer;
    }

    #sfPC .pc-controls button:focus-visible {
      outline:2px solid #38bdf8;
      outline-offset:3px;
    }

    @keyframes hardwarePulse {
      0%,100% {
        opacity:1;
        box-shadow:0 0 4px #fb718544;
      }
      50% {
        opacity:.65;
        box-shadow:0 0 15px #fb718599;
      }
    }

    @media (prefers-reduced-motion:reduce) {
      #sfPC .hotspot {
        animation:none !important;
      }
    }

    @media (max-width:420px) {
      #sfPC .hidden-parts {
        grid-template-columns:1fr;
      }
    }
  `;
  document.head.appendChild(style);

  box.innerHTML = `
    <div class="pc-scene">
      <svg class="pc-lines"
           viewBox="0 0 100 100"
           preserveAspectRatio="none"
           aria-hidden="true">

        <g fill="none" stroke="#67e8f9" stroke-width=".35">
          <path d="M27 43 H36 L42.5 39.7"/>
          <path d="M69 27 H57 L52 35.6"/>
        </g>

        <g fill="#67e8f9" stroke="#071626" stroke-width=".25">
          <circle cx="42.5" cy="39.7" r=".65"/>
          <circle cx="52" cy="35.6" r=".65"/>
        </g>
      </svg>

      <span id="cpu-part" class="hotspot"
            title="CPU is beneath this cooler">
        CPU
      </span>

      <span id="ram-part" class="hotspot"
            title="Vertical RAM modules beside the CPU cooler">
        RAM
      </span>
    </div>

    <div class="hidden-parts">
      <div class="part-card">
        <span id="network-part" class="hotspot">LAN</span>
        <small>Rear LAN port — not visible in this view.</small>
      </div>

      <div class="part-card">
        <span id="storage-part" class="hotspot">SSD</span>
        <small>SSD location — not identifiable in this view.</small>
      </div>

      <div class="part-card">
        <span id="gpu-part" class="hotspot">GPU</span>
        <small>Graphics card / display adapter.</small>
      </div>

      <div class="part-card">
        <span id="usb-part" class="hotspot">USB</span>
        <small>USB controller and rear USB port.</small>
      </div>

      <div class="part-card">
        <span id="audio-part" class="hotspot">AUDIO</span>
        <small>Audio output and speaker port.</small>
      </div>
    </div>

    <div class="pc-controls">
      <button type="button" id="pc-sound">Beep: OFF</button>
      <button type="button" id="pc-blink">Blinking: ON</button>
    </div>

    <p class="pc-note" id="pc-audio-status" role="status"></p>

    <p class="pc-note">
      CPU label points to its cooler. Beep is a classroom simulation,
      not a universal BIOS beep code.
    </p>
  `;

  box.querySelector('.pc-scene').prepend(photo);

  let soundOn = false;
  let blinkOn = !matchMedia('(prefers-reduced-motion: reduce)').matches;
  let audio = null;
  let repeat = null;
  let audioError = '';

  const voices = new Set();
  const soundButton = document.getElementById('pc-sound');
  const blinkButton = document.getElementById('pc-blink');
  const audioStatus = document.getElementById('pc-audio-status');

  function stopSound() {
    clearInterval(repeat);
    repeat = null;

    for (const voice of voices) {
      try {
        voice.stop();
      } catch (_) {}

      voice.disconnect();
    }

    voices.clear();
  }

  function beep() {
    const oscillator = audio.createOscillator();
    const gain = audio.createGain();
    const now = audio.currentTime;

    oscillator.type = 'sine';
    oscillator.frequency.value = 700;

    gain.gain.setValueAtTime(0, now);
    gain.gain.linearRampToValueAtTime(.05, now + .025);
    gain.gain.setValueAtTime(.05, now + .8);
    gain.gain.linearRampToValueAtTime(0, now + .85);

    oscillator.connect(gain);
    gain.connect(audio.destination);

    oscillator.onended = () => {
      voices.delete(oscillator);
      oscillator.disconnect();
      gain.disconnect();
    };

    voices.add(oscillator);
    oscillator.start(now);
    oscillator.stop(now + .9);
  }

  function sync() {
    const active = Boolean(current && !resolved && timeLeft > 0);
    const ramFault = active && current.correctFix === 'ram';

    box.dataset.active = String(active && !document.hidden);
    box.dataset.blink = String(blinkOn);

    soundButton.textContent = soundOn
      ? 'Beep: ON — mute'
      : 'Beep: OFF — enable';

    blinkButton.textContent = blinkOn
      ? 'Blinking: ON'
      : 'Blinking: OFF';

    soundButton.setAttribute('aria-pressed', String(soundOn));
    blinkButton.setAttribute('aria-pressed', String(blinkOn));

    const canPlay =
      ramFault &&
      soundOn &&
      !document.hidden &&
      audio &&
      audio.state === 'running';

    let message;

    if (canPlay) {
      if (repeat === null) {
        beep();
        repeat = setInterval(beep, 1800);
      }

      message = 'RAM fault: simulated long beep repeating.';
    } else {
      stopSound();

      message = audioError || (
        resolved
          ? 'Repair completed — effects stopped.'
          : current && timeLeft <= 0
            ? 'Time expired — effects stopped.'
            : ramFault
              ? 'RAM fault: click Beep to enable sound.'
              : 'Beep is available for the RAM / beeping scenario only.'
      );
    }

    if (audioStatus.textContent !== message) {
      audioStatus.textContent = message;
    }
  }

  async function unlockSound() {
    if (!soundOn) return;

    try {
      const AudioAPI = window.AudioContext || window.webkitAudioContext;

      if (!AudioAPI) {
        throw new Error('Audio unavailable');
      }

      if (!audio) {
        audio = new AudioAPI();
      }

      if (audio.state === 'suspended') {
        await audio.resume();
      }

      if (audio.state !== 'running') {
        throw new Error('Audio blocked');
      }

      audioError = '';
    } catch (_) {
      soundOn = false;
      audioError = 'Audio unavailable or blocked. Click Beep to retry.';
    }

    sync();
  }

  soundButton.onclick = () => {
    soundOn = !soundOn;
    audioError = '';

    if (soundOn) {
      unlockSound();
    }

    sync();
  };

  blinkButton.onclick = () => {
    blinkOn = !blinkOn;
    sync();
  };

  document.addEventListener('click', event => {
    if (event.target.closest(
      '#scenario-list button, .btn-action, .btn.secondary'
    )) {
      if (soundOn) {
        unlockSound();
      }

      sync();
    }
  });

  const observer = new MutationObserver(sync);

  ['cpu-part', 'ram-part', 'network-part', 'storage-part',
   'gpu-part', 'usb-part', 'audio-part'].forEach(id => {
    observer.observe(document.getElementById(id), {
      attributes:true,
      attributeFilter:['class']
    });
  });

  observer.observe(document.getElementById('timer'), {
    childList:true,
    characterData:true,
    subtree:true
  });

  document.addEventListener('visibilitychange', sync);
  window.addEventListener('pagehide', stopSound);

  sync();
})();
</script>
<script>
(() => {
  const box = document.getElementById('sfPC');
  const scene = box && box.querySelector('.pc-scene');
  const photo = scene && scene.querySelector('img');

  if (!photo) {
    console.error('Kod beep dan blinking perlu dipasang dahulu.');
    return;
  }

  const positions = [
    ['cpu-part', 'CPU', 12, 47],
    ['ram-part', 'RAM', 37, 19],
    ['gpu-part', 'GPU', 29, 60],
    ['network-part', 'LAN', 88, 17],
    ['usb-part', 'USB', 61, 30],
    ['audio-part', 'AUDIO', 61, 40],
    ['storage-part', 'SSD', 80, 94]
  ];

  const parts = positions.map(
    ([id]) => document.getElementById(id)
  );

  if (parts.some(part => !part)) return;

  const preview = new Image();

  preview.onload = () => {
    photo.src = preview.src;
    photo.alt =
      'PC with CPU and RAM; separate reference views of a LAN port and SSD.';

    scene.querySelectorAll('.pc-lines').forEach(
      line => line.remove()
    );

    parts.forEach((part, index) => {
      const [, label, left, top] = positions[index];

      part.textContent = label;
      part.classList.remove('hardware-photo');

      part.style.cssText =
        'position:absolute;' +
        'transform:translate(-50%,-50%);' +
        'width:auto;height:auto;' +
        'left:' + left + '%;' +
        'top:' + top + '%;';

      scene.appendChild(part);
    });

    box.querySelectorAll('.hidden-parts').forEach(
      row => row.remove()
    );

    const svg = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'svg'
    );

    svg.setAttribute('class', 'pc-lines');
    svg.setAttribute('viewBox', '0 0 100 100');
    svg.setAttribute('preserveAspectRatio', 'none');
    svg.setAttribute('aria-hidden', 'true');

    svg.innerHTML = `
      <g fill="none" stroke="#67e8f9" stroke-width=".3">
        <path d="M12 47 H17 L21.6 34.5"/>
        <path d="M37 19 H33 L28.8 31.4"/>
        <path d="M29 60 H34 L40 54"/>
        <path d="M88 17 H83 L77 28"/>
        <path d="M61 30 H65 L68.5 30"/>
        <path d="M61 40 H68 L75 41"/>
        <path d="M80 94 V77"/>
      </g>

      <g fill="#67e8f9" stroke="#071626" stroke-width=".2">
        <circle cx="21.6" cy="34.5" r=".6"/>
        <circle cx="28.8" cy="31.4" r=".6"/>
        <circle cx="40" cy="54" r=".6"/>
        <circle cx="77" cy="28" r=".6"/>
        <circle cx="68.5" cy="30" r=".6"/>
        <circle cx="75" cy="41" r=".6"/>
        <circle cx="80" cy="77" r=".6"/>
      </g>
    `;

    scene.appendChild(svg);

    const style = document.createElement('style');

    style.textContent = `
      #sfPC {
        max-width:820px;
      }

      #sfPC .pc-scene {
        position:relative;
      }

      #sfPC .pc-scene > img {
        width:100%;
        height:auto;
        display:block;
      }

      #sfPC .pc-lines {
        z-index:1;
        pointer-events:none;
      }

      #sfPC .hotspot {
        z-index:2;
      }
    `;

    document.head.appendChild(style);

    const caption = document.createElement('p');
    caption.className = 'pc-note';
    caption.textContent =
      'LAN, USB, AUDIO and SSD are reference views; GPU points to the graphics-card area.';

    scene.after(caption);

    document.getElementById('network-part').title =
      'LAN — example rear-port view';

    document.getElementById('storage-part').title =
      'SSD — example component view';

    document.getElementById('gpu-part').title =
      'GPU — graphics card / display adapter';

    document.getElementById('usb-part').title =
      'USB — rear USB port / controller';

    document.getElementById('audio-part').title =
      'AUDIO — speaker output port';
  };

  preview.onerror = () => {
    const note = document.createElement('p');
    note.className = 'pc-note';
    note.textContent =
      'Gambar tidak dijumpai. Semak fail assets/pc_gabungan.png.';

    box.appendChild(note);
  };

preview.src = 'assets/pc_gabungan.png';
})();
</script>
<script>
(() => {
  let currentLanguage = 'en';
  let lastDiagnosticType = null;

  const ui = {
    en: {
      subtitle:'INTERACTIVE COMPUTER TROUBLESHOOTING SIMULATOR', selectScenario:'1. Select Scenario', leaderboard:'🏆 Leaderboard', name:'Name',
      workspace:'2. Diagnostics & Repair Workspace', selectCase:'SELECT CASE', totalScore:'TOTAL SCORE', timeRemaining:'TIME REMAINING',
      selectStart:'Select a scenario to start', systemState:'SYSTEM STATE', diagnosticReading:'DIAGNOSTIC READING',
      stepA:'Step A: Run Diagnostic Tools', stepB:'Step B: Apply Fix Action', achievement:'🏅 Achievement Center', logout:'Logout',
      reset:'🔄 Reset Current Case', scenario:'Scenario', caseLabel:'CASE', completed:'Completed', issue:'ISSUE',
      faultUnresolved:'FAULT UNRESOLVED', systemRestored:'SYSTEM RESTORED', faultActive:'FAULT ACTIVE', operational:'OPERATIONAL',
      runDiagnostic:'Run Diagnostic', noReading:'No reading', instruction:'SCENARIO INSTRUCTION', scenarioCompleted:'SCENARIO COMPLETED ✓',
      completionMessage:'Correct repair action applied successfully. The system has been restored.', ready:'> SmartFix Ready. Choose a scenario to begin.',
      selected:'> Scenario Selected: ', readyTest:'\n> Ready for diagnostic testing.', selectFirst:'> ERROR: Select a scenario first.',
      alreadyResolved:'> SYSTEM NOTICE: This case is already resolved in this session.', diagnostic:'> DIAGNOSTIC RESULT\n  ',
      repairSuccess:'> REPAIR SUCCESS\n  Correct repair action applied. System restored.', repairFailed:'> REPAIR FAILED\n  The selected repair action is incorrect. Try again.',
      resetSuccess:'> CASE RESET SUCCESSFULLY\n> Run diagnostics and repair again.', timerExpired:'> TIMER EXPIRED: Diagnostic session ended.',
      error:'> ERROR: Unable to complete the request.', xpEarned:' XP earned.', replay:'Case replayed: XP was already awarded previously.', solveCase:'Solve Case #'
    },
    bm: {
      subtitle:'SIMULATOR INTERAKTIF PENYELESAIAN MASALAH KOMPUTER', selectScenario:'1. Pilih Senario', leaderboard:'🏆 Papan Kedudukan', name:'Nama',
      workspace:'2. Ruang Kerja Diagnostik & Pembaikan', selectCase:'PILIH KES', totalScore:'JUMLAH MARKAH', timeRemaining:'MASA BERBAKI',
      selectStart:'Pilih senario untuk bermula', systemState:'KEADAAN SISTEM', diagnosticReading:'BACAAN DIAGNOSTIK',
      stepA:'Langkah A: Jalankan Alat Diagnostik', stepB:'Langkah B: Laksanakan Tindakan Pembaikan', achievement:'🏅 Pusat Pencapaian', logout:'Log Keluar',
      reset:'🔄 Tetapkan Semula Kes Semasa', scenario:'Senario', caseLabel:'KES', completed:'Selesai', issue:'ISU',
      faultUnresolved:'KEROSAKAN BELUM DISELESAIKAN', systemRestored:'SISTEM DIPULIHKAN', faultActive:'KEROSAKAN AKTIF', operational:'BEROPERASI',
      runDiagnostic:'Jalankan Diagnostik', noReading:'Tiada bacaan', instruction:'ARAHAN SENARIO', scenarioCompleted:'SENARIO SELESAI ✓',
      completionMessage:'Tindakan pembaikan yang betul telah dilaksanakan. Sistem berjaya dipulihkan.', ready:'> SmartFix Sedia. Pilih senario untuk bermula.',
      selected:'> Senario Dipilih: ', readyTest:'\n> Sedia untuk menjalankan ujian diagnostik.', selectFirst:'> RALAT: Pilih senario terlebih dahulu.',
      alreadyResolved:'> NOTIS SISTEM: Kes ini telah diselesaikan dalam sesi ini.', diagnostic:'> KEPUTUSAN DIAGNOSTIK\n  ',
      repairSuccess:'> PEMBAIKAN BERJAYA\n  Tindakan pembaikan betul. Sistem berjaya dipulihkan.', repairFailed:'> PEMBAIKAN GAGAL\n  Tindakan pembaikan yang dipilih tidak betul. Cuba lagi.',
      resetSuccess:'> KES BERJAYA DITETAPKAN SEMULA\n> Jalankan diagnostik dan pembaikan sekali lagi.', timerExpired:'> MASA TAMAT: Sesi diagnostik telah berakhir.',
      error:'> RALAT: Permintaan tidak dapat diselesaikan.', xpEarned:' XP diperoleh.', replay:'Kes dimainkan semula: XP telah diberikan sebelum ini.', solveCase:'Selesaikan Kes #'
    }
  };

  const scenarioText = {
    1:{type:'beep',en:{title:'Blank Screen & Continuous Beeping',desc:'The computer powers on, but the screen remains blank and continuous beeping is heard.',instruction:'Run Check Beep Codes, observe the diagnostic result, identify the RAM fault, and choose the correct repair action.',diagnostic:'Continuous beep code detected — the RAM module is not seated correctly.'},bm:{title:'Skrin Kosong & Bunyi Beep Berterusan',desc:'Komputer dihidupkan tetapi skrin kekal kosong dan bunyi beep berterusan kedengaran.',instruction:'Jalankan Semakan Kod Beep, perhatikan keputusan diagnostik, kenal pasti masalah RAM dan pilih tindakan pembaikan yang betul.',diagnostic:'Kod beep berterusan dikesan — modul RAM tidak dipasang dengan betul.'}},
    2:{type:'thermal',en:{title:'System Thermal Shutdown During Use',desc:'The computer becomes excessively hot and shuts down while it is being used.',instruction:'Run Thermal / Hardware Sensor, observe the CPU temperature, and choose the correct repair action.',diagnostic:'CPU temperature: 96°C — the safe thermal limit has been exceeded.'},bm:{title:'Sistem Terpadam Akibat Haba Semasa Digunakan',desc:'Komputer menjadi terlalu panas dan terpadam semasa sedang digunakan.',instruction:'Jalankan Sensor Haba / Perkakasan, perhatikan suhu CPU dan pilih tindakan pembaikan yang betul.',diagnostic:'Suhu CPU: 96°C — had suhu selamat telah dilepasi.'}},
    3:{type:'network',en:{title:'Unidentified Network / No Internet Access',desc:'The computer is connected to a network but cannot access the Internet.',instruction:'Run IP / Network Status, inspect the network configuration, and choose the correct repair action.',diagnostic:'IP address: 169.254.x.x — a valid address was not assigned by DHCP.'},bm:{title:'Rangkaian Tidak Dikenali / Tiada Akses Internet',desc:'Komputer disambungkan kepada rangkaian tetapi tidak dapat mengakses Internet.',instruction:'Jalankan Status IP / Rangkaian, periksa konfigurasi rangkaian dan pilih tindakan pembaikan yang betul.',diagnostic:'Alamat IP: 169.254.x.x — alamat yang sah tidak diberikan oleh DHCP.'}},
    4:{type:'boot',en:{title:'No Bootable Device Found',desc:'The computer cannot detect a device containing a bootable operating system.',instruction:'Run BIOS Boot Priority, inspect the detected boot device and boot order, and choose the correct repair action.',diagnostic:'Boot device not found — the BIOS boot order is incorrect.'},bm:{title:'Tiada Peranti Boleh Boot Ditemui',desc:'Komputer tidak dapat mengesan peranti yang mengandungi sistem operasi boleh boot.',instruction:'Jalankan Keutamaan Boot BIOS, periksa peranti boot dan susunan boot, kemudian pilih tindakan pembaikan yang betul.',diagnostic:'Peranti boot tidak ditemui — susunan boot BIOS tidak betul.'}},
    5:{type:'startup',en:{title:'Computer Running Extremely Slow',desc:'Applications take a long time to open and the computer frequently freezes.',instruction:'Run Check Startup Applications, identify the unnecessary high-impact applications, and choose the correct repair action.',diagnostic:'Startup impact: HIGH — 12 unnecessary applications launch automatically when the computer starts.',caption:'Scenario 5 visual: unnecessary startup applications are slowing down the computer.'},bm:{title:'Komputer Berjalan Sangat Perlahan',desc:'Aplikasi mengambil masa yang lama untuk dibuka dan komputer kerap menjadi beku.',instruction:'Jalankan Semakan Aplikasi Permulaan, kenal pasti aplikasi berimpak tinggi yang tidak diperlukan dan pilih tindakan pembaikan yang betul.',diagnostic:'Impak permulaan: TINGGI — 12 aplikasi yang tidak diperlukan dilancarkan secara automatik semasa komputer bermula.',caption:'Visual Senario 5: aplikasi permulaan yang tidak diperlukan memperlahankan komputer.'}},
    6:{type:'driver',en:{title:'Blue Screen and Unexpected Restart',desc:'A blue error screen appears and the computer restarts unexpectedly.',instruction:'Run Check System Error / Driver, observe the diagnostic result, and choose the most appropriate repair action.',diagnostic:'System log: a display-driver crash was detected after a recent driver update.',caption:'Scenario 6 visual: blue-screen system crash and unexpected restart.'},bm:{title:'Skrin Biru dan Mula Semula Tanpa Dijangka',desc:'Skrin ralat biru muncul dan komputer dimulakan semula tanpa dijangka.',instruction:'Jalankan Semakan Ralat Sistem / Pemacu, perhatikan keputusan diagnostik dan pilih tindakan pembaikan yang paling sesuai.',diagnostic:'Log sistem: kerosakan pemacu paparan dikesan selepas kemas kini pemacu terkini.',caption:'Visual Senario 6: kerosakan skrin biru dan mula semula tanpa dijangka.'}},
    7:{type:'usb',en:{title:'USB Device Not Detected',desc:'A USB flash drive is connected, but it does not appear in File Explorer.',instruction:'Run Check USB Device Status, identify why the flash drive is not detected, and choose the correct repair action.',diagnostic:'Device Manager: the USB device has a driver error (Code 28).',caption:'Scenario 7 visual: the connected USB device is not detected.'},bm:{title:'Peranti USB Tidak Dikesan',desc:'Pemacu kilat USB disambungkan tetapi tidak muncul dalam File Explorer.',instruction:'Jalankan Semakan Status Peranti USB, kenal pasti sebab pemacu kilat tidak dikesan dan pilih tindakan pembaikan yang betul.',diagnostic:'Pengurus Peranti: peranti USB mengalami ralat pemacu (Kod 28).',caption:'Visual Senario 7: peranti USB yang disambungkan tidak dikesan.'}},
    8:{type:'audio',en:{title:'No Sound from Speakers',desc:'Audio is playing, but no sound can be heard from the connected speakers.',instruction:'Run Check Audio Output, inspect the selected output device, and choose the correct repair action.',diagnostic:'Audio test: the speakers are connected, but the wrong output device is selected.',caption:'Scenario 8 visual: audio is playing but no sound is heard.'},bm:{title:'Tiada Bunyi daripada Pembesar Suara',desc:'Audio sedang dimainkan tetapi tiada bunyi kedengaran daripada pembesar suara.',instruction:'Jalankan Semakan Output Audio, periksa peranti output yang dipilih dan pilih tindakan pembaikan yang betul.',diagnostic:'Ujian audio: pembesar suara disambungkan tetapi peranti output yang salah dipilih.',caption:'Visual Senario 8: audio dimainkan tetapi tiada bunyi kedengaran.'}}
  };

  const issueNames = {en:{HARDWARE:'HARDWARE',NETWORK:'NETWORK',SOFTWARE:'SOFTWARE',PERIPHERAL:'PERIPHERAL',SYSTEM:'SYSTEM'},bm:{HARDWARE:'PERKAKASAN',NETWORK:'RANGKAIAN',SOFTWARE:'PERISIAN',PERIPHERAL:'PERANTI PERSISIAN',SYSTEM:'SISTEM'}};
  const diagnosticButtons = {
    en:{beep:'🔊 Check Beep Codes',thermal:'🌡️ Thermal / Hardware Sensor',network:'🌐 IP / Network Status',boot:'💽 BIOS Boot Priority',startup:'📊 Check Startup Applications',driver:'🖥️ Check System Error / Driver',usb:'🔌 Check USB Device Status',audio:'🔊 Check Audio Output'},
    bm:{beep:'🔊 Semak Kod Beep',thermal:'🌡️ Sensor Haba / Perkakasan',network:'🌐 Status IP / Rangkaian',boot:'💽 Keutamaan Boot BIOS',startup:'📊 Semak Aplikasi Permulaan',driver:'🖥️ Semak Ralat Sistem / Pemacu',usb:'🔌 Semak Status Peranti USB',audio:'🔊 Semak Output Audio'}
  };
  const fixButtons = {
    en:{ram:'🔧 Reseat RAM Module',paste:'🔧 Re-apply Thermal Paste',ip:'🔧 Renew IP (DHCP)',bios:'🔧 Reset BIOS Boot Order',startup:'🚀 Disable Unnecessary Startup Apps',driver:'🧩 Roll Back Faulty Display Driver',usb:'🔌 Reinstall USB Device Driver',audio:'🔊 Select Speakers as Default Output'},
    bm:{ram:'🔧 Pasang Semula Modul RAM',paste:'🔧 Sapukan Semula Pes Haba',ip:'🔧 Perbaharui IP (DHCP)',bios:'🔧 Tetapkan Semula Susunan Boot BIOS',startup:'🚀 Nyahaktifkan Aplikasi Permulaan Tidak Perlu',driver:'🧩 Kembalikan Pemacu Paparan Bermasalah',usb:'🔌 Pasang Semula Pemacu Peranti USB',audio:'🔊 Pilih Pembesar Suara sebagai Output Lalai'}
  };
  const achievementNames = {
    1:{en:'RAM Specialist',bm:'Pakar RAM'},2:{en:'Thermal Expert',bm:'Pakar Haba'},3:{en:'Network Rescuer',bm:'Penyelamat Rangkaian'},4:{en:'Boot Master',bm:'Pakar Boot'},
    5:{en:'Startup Optimizer',bm:'Pengoptimum Permulaan'},6:{en:'Blue Screen Resolver',bm:'Penyelesai Skrin Biru'},7:{en:'USB Troubleshooter',bm:'Penyelesai Masalah USB'},8:{en:'Audio Restorer',bm:'Pemulih Audio'}
  };

  const t = key => ui[currentLanguage][key] || key;
  const copyFor = s => scenarioText[s.id] ? scenarioText[s.id][currentLanguage] : {title:s.title,desc:s.desc,instruction:'',diagnostic:''};
  const setButtonText = (selector,value) => { const el=document.querySelector(selector); if(el) el.textContent=value; };

  const baseInit = init;
  init = function() {
    baseInit();
    document.querySelectorAll('#scenario-list .scenario-btn').forEach((button,index) => {
      const s=scenarios[index]; if(!s) return;
      const copy=copyFor(s), label=button.querySelector('.scenario-label'), summary=button.querySelector('.scenario-summary'), check=button.querySelector('.scenario-check');
      if(label) label.textContent=t('scenario')+' '+s.id;
      if(summary) summary.textContent=copy.title;
      if(check) check.setAttribute('aria-label',t('completed'));
    });
  };

  renderScenarioSupport = function(s) {
    const support=scenarioSupport[s.correctFix], copy=copyFor(s), instruction=document.getElementById('scenario-instruction');
    const visual=document.getElementById('scenario-visual'), image=document.getElementById('scenario-visual-img'), caption=document.getElementById('scenario-visual-caption'), pc=document.querySelector('.pc-wrapper');
    if(!support){instruction.hidden=true;visual.hidden=true;}
    else {
      instruction.innerHTML='<strong>'+t('instruction')+'</strong>'+copy.instruction; instruction.hidden=false;
      if(support.image){image.src=support.image;image.alt=copy.title;caption.textContent=copy.caption||support.caption||'';visual.hidden=false;}
      else {image.removeAttribute('src');image.alt='';caption.textContent='';visual.hidden=true;}
    }
    if(pc){pc.hidden=false;pc.style.display='';}
  };

  setInstructionState = function(isResolved) {
    if(!current) return;
    const instruction=document.getElementById('scenario-instruction'), copy=copyFor(current);
    instruction.classList.toggle('resolved',isResolved);
    instruction.innerHTML=isResolved?'<strong>'+t('scenarioCompleted')+'</strong>'+t('completionMessage'):'<strong>'+t('instruction')+'</strong>'+copy.instruction;
    instruction.hidden=false;
  };

  startTimer = function() {
    clearInterval(timerInterval); timeLeft=299; drawTime();
    timerInterval=setInterval(()=>{timeLeft--;drawTime();if(timeLeft<=0){clearInterval(timerInterval);log(t('timerExpired'));}},1000);
  };

  selectScenario = function(s) {
    current=s;resolved=false;lastDiagnosticType=null;const copy=copyFor(s);
    resetHighlights();setPart(faultFor(s),'fault');renderScenarioSupport(s);setInstructionState(false);
    document.getElementById('case-number').textContent=t('caseLabel')+' #'+s.id;
    const issueKey=String(s.issue_type||'SYSTEM').toUpperCase();
    document.getElementById('issue-type').textContent=(issueNames[currentLanguage][issueKey]||issueKey)+' '+t('issue');
    document.getElementById('difficulty').textContent='★'.repeat(s.difficulty)+'☆'.repeat(5-s.difficulty);
    document.getElementById('scenario-title').textContent=copy.title;document.getElementById('scenario-desc').textContent=copy.desc;
    document.getElementById('scenario-status').innerHTML='<span class="badge unresolved">'+t('faultUnresolved')+'</span>';
    document.getElementById('metric-state').textContent=t('faultActive');document.getElementById('metric-reading').textContent=t('runDiagnostic');
    log(t('selected')+copy.title+t('readyTest'));startTimer();init();
  };

  runDiagnostic = function(type) {
    if(!current){log(t('selectFirst'));return;}
    lastDiagnosticType=type;const meta=scenarioText[current.id],copy=copyFor(current),result=meta&&meta.type===type?copy.diagnostic:t('noReading');
    document.getElementById('metric-reading').textContent=result;log(t('diagnostic')+result);
  };

  applyFix = async function(fixType) {
    if(!current){log(t('selectFirst'));return;} if(resolved){log(t('alreadyResolved'));return;}
    try {
      const response=await fetch('api_fix.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({scenario_id:current.id,fix_type:fixType,time_taken:299-timeLeft})});
      const data=await response.json(); if(!data.ok){log(t('error'));return;} if(!data.correct){log(t('repairFailed'));return;}
      resolved=true;clearInterval(timerInterval);resetHighlights();setPart(faultFor(current),'resolved');completed.add(current.id);
      document.getElementById('scenario-status').innerHTML='<span class="badge resolved">'+t('systemRestored')+'</span>';
      document.getElementById('metric-state').textContent=t('operational');setInstructionState(true);
      document.getElementById('score-value').textContent=data.total_xp+' XP';document.getElementById('header-xp').textContent=data.total_xp+' XP';
      const badge=document.getElementById('badge-'+current.id);if(badge){badge.classList.add('unlocked');badge.children[0].textContent='🏅';}
      log(t('repairSuccess')+(data.xp_added?'\n> +'+data.xp_added+t('xpEarned'):'\n> '+t('replay')));init();translateAchievements();
    } catch(_){log(t('error'));}
  };

  resetCase = function() {
    if(!current){log(t('selectFirst'));return;} resolved=false;lastDiagnosticType=null;resetHighlights();setPart(faultFor(current),'fault');
    document.getElementById('scenario-status').innerHTML='<span class="badge unresolved">'+t('faultUnresolved')+'</span>';
    document.getElementById('metric-state').textContent=t('faultActive');document.getElementById('metric-reading').textContent=t('runDiagnostic');
    setInstructionState(false);startTimer();log(t('resetSuccess'));
  };

  function translateAchievements() {
    Object.entries(achievementNames).forEach(([id,names])=>{
      const badge=document.getElementById('badge-'+id);if(!badge)return;
      const title=badge.querySelector('strong'),description=badge.querySelector('.muted');
      if(title) title.textContent=names[currentLanguage];if(description) description.textContent=t('solveCase')+id;
    });
  }

  function applyStaticLanguage() {
    document.documentElement.lang=currentLanguage==='bm'?'ms':'en';
    const subtitle=document.querySelector('.topbar > div:first-child .muted');if(subtitle)subtitle.textContent=t('subtitle');
    const panels=document.querySelectorAll('aside > .panel');if(panels[0])panels[0].querySelector('h2').textContent=t('selectScenario');if(panels[1])panels[1].querySelector('h2').textContent=t('leaderboard');
    const nameHeader=document.querySelector('.leaderboard th:first-child');if(nameHeader)nameHeader.textContent=t('name');
    const mainHeadings=document.querySelectorAll('main.panel > h2');if(mainHeadings[0])mainHeadings[0].textContent=t('workspace');if(mainHeadings[1])mainHeadings[1].textContent=t('achievement');
    const caseLabels=document.querySelectorAll('.case-head .muted');if(caseLabels[0])caseLabels[0].textContent=t('totalScore');if(caseLabels[1])caseLabels[1].textContent=t('timeRemaining');
    const metricLabels=document.querySelectorAll('.metric .muted');if(metricLabels[0])metricLabels[0].textContent=t('systemState');if(metricLabels[1])metricLabels[1].textContent=t('diagnosticReading');
    const headings=document.querySelectorAll('main.panel > h3');if(headings[1])headings[1].textContent=t('stepA');if(headings[2])headings[2].textContent=t('stepB');
    const logout=document.querySelector('a[href="logout.php"]');if(logout)logout.textContent=t('logout');
    Object.entries(diagnosticButtons[currentLanguage]).forEach(([key,value])=>setButtonText(`button[onclick="runDiagnostic('${key}')"]`,value));
    Object.entries(fixButtons[currentLanguage]).forEach(([key,value])=>setButtonText(`button[onclick="applyFix('${key}')"]`,value));
    setButtonText('button[onclick="resetCase()"]',t('reset'));
    document.getElementById('lang-bm').classList.toggle('active',currentLanguage==='bm');document.getElementById('lang-en').classList.toggle('active',currentLanguage==='en');
    document.getElementById('lang-bm').setAttribute('aria-pressed',String(currentLanguage==='bm'));document.getElementById('lang-en').setAttribute('aria-pressed',String(currentLanguage==='en'));
  }

  function translateCurrentScenario() {
    if(!current){document.getElementById('case-number').textContent=t('caseLabel')+' #--';document.getElementById('issue-type').textContent=t('selectCase');document.getElementById('scenario-title').textContent=t('selectStart');document.getElementById('metric-state').textContent='--';document.getElementById('metric-reading').textContent='--';log(t('ready'));return;}
    const copy=copyFor(current),issueKey=String(current.issue_type||'SYSTEM').toUpperCase(),meta=scenarioText[current.id];
    document.getElementById('case-number').textContent=t('caseLabel')+' #'+current.id;document.getElementById('issue-type').textContent=(issueNames[currentLanguage][issueKey]||issueKey)+' '+t('issue');
    document.getElementById('scenario-title').textContent=copy.title;document.getElementById('scenario-desc').textContent=copy.desc;
    document.getElementById('scenario-status').innerHTML='<span class="badge '+(resolved?'resolved':'unresolved')+'">'+(resolved?t('systemRestored'):t('faultUnresolved'))+'</span>';
    document.getElementById('metric-state').textContent=resolved?t('operational'):t('faultActive');
    document.getElementById('metric-reading').textContent=lastDiagnosticType?(meta&&meta.type===lastDiagnosticType?copy.diagnostic:t('noReading')):t('runDiagnostic');
    renderScenarioSupport(current);setInstructionState(resolved);log(resolved?t('repairSuccess'):t('selected')+copy.title+t('readyTest'));
  }

  function setLanguage(language) {
    currentLanguage=language==='bm'?'bm':'en';try{localStorage.setItem('smartfixLanguage',currentLanguage);}catch(_){}
    applyStaticLanguage();init();translateAchievements();translateCurrentScenario();
  }

  document.getElementById('lang-bm').onclick=()=>setLanguage('bm');document.getElementById('lang-en').onclick=()=>setLanguage('en');
  let savedLanguage='en';try{savedLanguage=localStorage.getItem('smartfixLanguage')||'en';}catch(_){}
  setLanguage(savedLanguage);
})();
</script>
</body>
</html>
