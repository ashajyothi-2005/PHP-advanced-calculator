<?php
session_start();

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

$current = ""; 
if (isset($_POST['input'])) { $current = $_POST['input']; }

if (isset($_POST['btn'])) {
    $btn_val = $_POST['btn'];

    if ($btn_val == "=") {
        $expression = str_replace(['x', '÷'], ['*', '/'], $current);
        try {
            // Using @ to suppress errors for incomplete math strings
            $result = @eval("return $expression;");
            if ($result !== false && $result !== null) {
                $log = $current . " = " . $result;
                array_unshift($_SESSION['history'], $log);
                $current = $result;
            }
        } catch (Throwable $e) {
            $current = "Error";
        }
    } elseif ($btn_val == "C") {
        $current = "";
    } elseif ($btn_val == "DEL") {
        $current = substr($current, 0, -1);
    } elseif ($btn_val == "Clear History") {
        $_SESSION['history'] = [];
    } elseif (in_array($btn_val, ['sqrt', 'pow2', 'sin', 'cos'])) {
        // Advanced Math Functions
        if (is_numeric($current)) {
            switch ($btn_val) {
                case 'sqrt': $current = sqrt($current); break;
                case 'pow2': $current = pow($current, 2); break;
                case 'sin':  $current = sin(deg2rad($current)); break;
                case 'cos':  $current = cos(deg2rad($current)); break;
            }
        }
    } else {
        $current .= $btn_val;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced PHP Calculator</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 30px; background-color: #f4f7f6; }
        .calc-container { width: 340px; background: #ffffff; padding: 20px; border-radius: 20px; border: 1px solid #e0e0e0; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .screen { width: 100%; height: 70px; background: #fff; border: 1px solid #ddd; margin-bottom: 15px; text-align: right; font-size: 1.8rem; padding: 15px; box-sizing: border-box; border-radius: 12px; color: #333; }
        .btn-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        button { height: 55px; border-radius: 10px; border: 1px solid #f0f0f0; font-size: 1rem; cursor: pointer; background: #fff; transition: 0.2s; }
        button:hover { background: #f8f9fa; }
        .op { background: #f1f3f5; font-weight: bold; }
        .equal { background: #2d3436; color: #fff; border: none; }
        .clear-btn { background: #fee2e2; color: #991b1b; }
        .math-toggle-btn { background: #e0f2fe; color: #0369a1; font-weight: bold; }

        /* Hidden Sections Logic */
        #math-options, #history-content { display: none; margin-top: 15px; padding: 10px; border-radius: 10px; border: 1px solid #eee; background: #fafafa; }
        #show-math:checked ~ #math-options { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        #show-history:checked ~ #history-content { display: block; }
        #show-math, #show-history { display: none; }
        
        .math-func { background: #f0f9ff; border: 1px solid #bae6fd; height: 45px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="calc-container">
    <form method="post">
        <input type="text" class="screen" value="<?php echo $current ?: '0'; ?>" readonly>
        <input type="hidden" name="input" value="<?php echo $current; ?>">

        <div class="btn-grid">
            <button type="submit" name="btn" value="C" class="clear-btn">C</button>
            <button type="submit" name="btn" value="DEL" style="background:#fff7ed;">⌫</button>
            
            <button type="button" class="math-toggle-btn" onclick="document.getElementById('show-math').click();">Math</button>
            <button type="button" onclick="document.getElementById('show-history').click();">📜</button>

            <button type="submit" name="btn" value="7">7</button>
            <button type="submit" name="btn" value="8">8</button>
            <button type="submit" name="btn" value="9">9</button>
            <button type="submit" name="btn" value="/" class="op">÷</button>

            <button type="submit" name="btn" value="4">4</button>
            <button type="submit" name="btn" value="5">5</button>
            <button type="submit" name="btn" value="6">6</button>
            <button type="submit" name="btn" value="x" class="op">x</button>

            <button type="submit" name="btn" value="1">1</button>
            <button type="submit" name="btn" value="2">2</button>
            <button type="submit" name="btn" value="3">3</button>
            <button type="submit" name="btn" value="-" class="op">-</button>

            <button type="submit" name="btn" value="0">0</button>
            <button type="submit" name="btn" value=".">.</button>
            <button type="submit" name="btn" value="=" class="equal">=</button>
            <button type="submit" name="btn" value="+" class="op">+</button>
        </div>

        <input type="checkbox" id="show-math">
        <div id="math-options">
            <button type="submit" name="btn" value="sqrt" class="math-func">√ Square Root</button>
            <button type="submit" name="btn" value="pow2" class="math-func">x² Square</button>
            <button type="submit" name="btn" value="sin" class="math-func">sin</button>
            <button type="submit" name="btn" value="cos" class="math-func">cos</button>
        </div>

        <input type="checkbox" id="show-history">
        <div id="history-content">
            <small>History</small>
            <?php foreach ($_SESSION['history'] as $item): ?>
                <div style="font-size: 0.85rem; padding: 5px 0; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item); ?></div>
            <?php endforeach; ?>
            <button type="submit" name="btn" value="Clear History" style="width:100%; border:none; background:none; color:red; cursor:pointer; font-size:0.7rem; margin-top:5px;">Clear All</button>
        </div>
    </form>
</div>

</body>
</html> 