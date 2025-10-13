<?php
// Ultimate Dark Glassmorphism PHP Calculator - Final Version (No Warnings)

$resultDisplay = '';
$exprValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exprValue = $_POST['expr'] ?? '';
    $exprValue = trim($exprValue);

    if ($exprValue === '') {
        $resultDisplay = '';
    } else {
        
        $safe = str_replace(',', '.', $exprValue);

        
        $safe = preg_replace_callback('/([0-9]+(?:\.[0-9]+)?)\s*%/', function ($m) {
            return '(' . $m[1] . '/100)';
        }, $safe);

        
        $safe = str_replace(' ', '', $safe);

        
        if (preg_match('/[^0-9+\-*\/\(\).%]/', $safe)) {
            $resultDisplay = 'Error';
        } else {
           
            try {
                $evalResult = @eval('return ' . $safe . ';');
                if ($evalResult === false || is_nan($evalResult) || is_infinite($evalResult)) {
                    $resultDisplay = 'Error';
                } else {
                    if (is_float($evalResult)) {
                        $res = rtrim(rtrim(sprintf('%.10f', $evalResult), '0'), '.');
                    } else {
                        $res = (string)$evalResult;
                    }
                    $resultDisplay = $res;
                    $exprValue = $res; 
                }
            } catch (Throwable $e) {
                $resultDisplay = 'Error';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ultimate Calculator</title>
<style>
:root{
  --bg:#071026;
  --card:#0f1724;
  --glass: rgba(255,255,255,0.04);
  --muted: rgba(255,255,255,0.6);
  --accent: linear-gradient(135deg,#8b5cf6,#06b6d4);
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  background:
    radial-gradient(1200px 400px at 10% 10%, rgba(7,16,38,0.6), transparent 10%),
    radial-gradient(900px 300px at 90% 90%, rgba(2,6,23,0.6), transparent 10%),
    var(--bg);
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:28px;
  color:#e6eef8;
}
.calc-wrap{
  width:420px;
  max-width:96vw;
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  border-radius:20px;
  padding:20px;
  box-shadow: 0 10px 40px rgba(2,6,23,0.7), inset 0 1px 0 rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.04);
  backdrop-filter: blur(8px) saturate(1.05);
}
.display{
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  border-radius:14px;
  padding:14px 16px;
  min-height:84px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:flex-end;
  margin-bottom:18px;
  border:1px solid rgba(255,255,255,0.03);
}
.expr{
  font-size:14px;
  color:var(--muted);
  width:100%;
  text-align:right;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  margin-bottom:6px;
}
.value{
  font-size:34px;
  font-weight:700;
  letter-spacing:0.6px;
  width:100%;
  text-align:right;
}
.keys{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:12px;
}
.key{
  height:64px;
  border-radius:12px;
  border:0;
  cursor:pointer;
  font-size:18px;
  font-weight:600;
  color:#e6eef8;
  background: linear-gradient(180deg, rgba(7,12,20,0.9), rgba(2,6,12,0.85));
  box-shadow: 0 8px 18px rgba(2,6,23,0.55), inset 0 1px 0 rgba(255,255,255,0.02);
  transition: transform .06s ease, box-shadow .06s ease;
}
.key:active{ transform: translateY(3px); box-shadow: 0 4px 8px rgba(0,0,0,0.6); }
.key.op{
  background-image: var(--accent);
  color:#fff;
}
.key.ghost{
  background: linear-gradient(180deg, rgba(255,255,255,0.015), rgba(255,255,255,0.01));
  color:var(--muted);
  font-weight:600;
}
.key.equal{
  grid-column: span 2;
  height:64px;
  border-radius:12px;
  background: linear-gradient(180deg,#16a34a,#059669);
  font-size:20px;
  box-shadow: 0 10px 24px rgba(6,95,70,0.18);
}
.key.zero{
  grid-column: span 2;
}
.spacer{height:6px}
@media (max-width:420px){
  .calc-wrap{padding:16px}
  .value{font-size:28px}
  .key{height:56px;font-size:17px}
}
</style>
</head>
<body>
<div class="calc-wrap" role="application" aria-label="calculator">
  <form method="post" id="calcForm" style="margin:0">
    <div class="display">
      <div class="expr" id="expr"><?php echo htmlspecialchars($exprValue === '' ? '0' : $exprValue); ?></div>
      <div class="value" id="value"><?php echo ($resultDisplay !== '' ? htmlspecialchars($resultDisplay) : '0'); ?></div>
    </div>

    <input type="hidden" name="expr" id="exprInput" value="<?php echo htmlspecialchars($exprValue); ?>">

    <div class="keys">
      <button type="button" class="key ghost" onclick="clearAll()">C</button>
      <button type="button" class="key ghost" onclick="press('(')">(</button>
      <button type="button" class="key ghost" onclick="press(')')">)</button>
      <button type="button" class="key op" onclick="press('/')">÷</button>

      <button type="button" class="key" onclick="press('7')">7</button>
      <button type="button" class="key" onclick="press('8')">8</button>
      <button type="button" class="key" onclick="press('9')">9</button>
      <button type="button" class="key op" onclick="press('*')">×</button>

      <button type="button" class="key" onclick="press('4')">4</button>
      <button type="button" class="key" onclick="press('5')">5</button>
      <button type="button" class="key" onclick="press('6')">6</button>
      <button type="button" class="key op" onclick="press('-')">−</button>

      <button type="button" class="key" onclick="press('1')">1</button>
      <button type="button" class="key" onclick="press('2')">2</button>
      <button type="button" class="key" onclick="press('3')">3</button>
      <button type="button" class="key op" onclick="press('+')">+</button>

      <button type="button" class="key zero" onclick="press('0')">0</button>
      <button type="button" class="key" onclick="press(',')">,</button>
      <button type="button" class="key op" onclick="press('%')">%</button>
      <button type="submit" class="key equal">=</button>
    </div>
  </form>
  <div class="spacer"></div>
</div>

<script>
(function(){
  const exprInput = document.getElementById('exprInput');
  const exprLabel = document.getElementById('expr');
  const valueLabel = document.getElementById('value');

  function setExpr(v){
    exprInput.value = v;
    exprLabel.textContent = v === '' ? '0' : v;
  }

  window.press = function(ch){
    let cur = exprInput.value || '';
    cur += ch;
    setExpr(cur);
  };

  window.clearAll = function(){
    setExpr('');
    valueLabel.textContent = '0';
  };

  window.addEventListener('keydown', function(e){
    const k = e.key;
    if (/^[0-9]$/.test(k)) { press(k); }
    else if ('+-*/'.includes(k)) { press(k); }
    else if (k === 'Enter') { document.getElementById('calcForm').submit(); }
    else if (k === 'Backspace') {
      let s = exprInput.value || '';
      s = s.slice(0,-1);
      setExpr(s);
    }
    else if (k === ',' || k === '.') { press(','); }
    else if (k === '%') { press('%'); }
    else if (k === '(' || k === ')') { press(k); }
  });
})();
</script>
</body>
</html>
