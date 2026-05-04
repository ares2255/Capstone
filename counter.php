<?php
session_start();
include_once "config/db.php";

// This pulls your latest rates from the settings table
$settings_query = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
$rates = mysqli_fetch_assoc($settings_query);

if(!isset($_SESSION['username']) && !isset($_SESSION['admin_username'])){
    header("Location: index.php");
    exit();
}

$is_admin = isset($_SESSION['admin_username']);
$display_user = $is_admin ? $_SESSION['admin_username'] : $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Desktop | Counter</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* 1. LAYOUT LOCK: Matches Printing.php to stop shifting */
        html { 
            overflow-y: scroll; 
            scrollbar-gutter: stable; 
        }

        body { 
            background-color: #050b14; 
            background-image: 
                linear-gradient(rgba(19, 39, 66, 0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(19, 39, 66, 0.3) 1px, transparent 1px);
            background-size: 50px 50px; 
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; 
            min-height: 100vh; 
        }

        /* 2. HEADER LOCK: Standardized height (70px) and positioning */
        .header { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 12px 30px; background-color: #0a0e14; 
            border-bottom: 1px solid #1e293b; position: relative; 
            height: 70px; box-sizing: border-box;
        }

        .header::after { 
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; 
            height: 1px; background: linear-gradient(90deg, transparent, #ff4d4d, transparent); 
        }

        .logo-container { display: flex; align-items: center; gap: 10px; }
        .logo-icon { 
            color: #ff4d4d; font-size: 18px; border: 2px solid #ff4d4d; 
            border-radius: 50%; width: 28px; height: 28px; 
            display: flex; align-items: center; justify-content: center; 
        }
        .logo-text { font-weight: bold; font-size: 20px; color: #38bdf8; }
        .logo-text span { color: #ff4d4d; }

        .nav-links { display: flex; gap: 35px; flex-shrink: 0; }
        .nav-item { 
            text-decoration: none; color: #94a3b8; 
            display: flex; align-items: center; gap: 8px; 
            font-size: 14px; padding: 8px 0; transition: 0.3s; 
            border-bottom: 2px solid transparent; 
        }
        .nav-item.active { color: #ff4d4d; border-bottom: 2px solid #ff4d4d; }
        .nav-item:hover { color: #ffffff; }

        .header-right { display: flex; align-items: center; gap: 15px; }
        #systemClock { 
            color: #38bdf8; font-family: monospace; 
            background: rgba(56, 189, 248, 0.1); 
            padding: 5px 12px; border-radius: 4px; 
            font-weight: bold; font-size: 14px; 
        }
        
        .admin-badge { 
            background: #1e293b; color: #94a3b8; 
            padding: 6px 12px; border-radius: 5px; 
            font-size: 13px; display: flex; align-items: center; gap: 6px; 
        }
        
        .logout-btn { 
            background: #ff4d4d; color: white; border: none; 
            padding: 7px 16px; border-radius: 6px; 
            cursor: pointer; text-decoration: none; 
            font-weight: bold; font-size: 13px; 
        }
        
        /* Grid and Cards */
        .main-container { max-width: 1400px; margin: 0 auto; padding: 40px; }
        .pc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        .pc-card { background: rgba(10, 25, 47, 0.8); border: 1px solid #132742; padding: 25px; border-radius: 12px; cursor: pointer; text-align: center; transition: 0.3s; }
        .pc-card.active-pc { border-color: #ff4d4d; box-shadow: 0 0 20px rgba(255, 77, 77, 0.4); background: rgba(255, 77, 77, 0.05); transform: scale(1.02); }
        .active-pc h3 { color: #ff4d4d; }
        .timer-display { color: #f1c40f; font-size: 18px; font-weight: bold; font-family: monospace; margin-top: 10px; }
        
        /* Modals */
        .modal-btn { background: #1e293b; color: white; border: 1px solid #38bdf8; padding: 10px; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        .modal-btn:hover { background: #38bdf8; color: #0a0e14; }
        .modal-btn-open { grid-column: span 2; background: #ff4d4d; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        
        .custom-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); display: flex; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(5px); }
        .custom-modal-content { background: #0f172a; border: 1px solid #1e293b; padding: 30px; border-radius: 15px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .modal-icon-red { color: #ff4d4d; font-size: 50px; margin-bottom: 20px; }
        .custom-modal-content h2 { margin: 0 0 10px; color: white; font-weight: bold; }
        .custom-modal-content p { color: #94a3b8; font-size: 14px; line-height: 1.5; }
        .modal-footer-btns { display: flex; gap: 15px; margin-top: 25px; }
        .btn-cancel { flex: 1; padding: 12px; border-radius: 8px; border: none; background: #1e293b; color: #94a3b8; cursor: pointer; transition: 0.3s; }
        .btn-confirm-red { flex: 1; padding: 12px; border-radius: 8px; border: none; background: #ff4d4d; color: white; cursor: pointer; font-weight: bold; transition: 0.3s; }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo-container">
            <div class="logo-icon"><i class="fas fa-desktop"></i></div>
            <div class="logo-text">The<span>Desktop</span></div>
        </div>
        <nav class="nav-links">
            <a href="counter.php" class="nav-item active"><i class="fas fa-list-ul"></i> Counter</a>
            <a href="printing.php" class="nav-item"><i class="fas fa-print"></i> Printing</a>
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <div class="header-right">
            <div id="systemClock">00:00:00 AM</div>
            <div class="admin-badge"><i class="fas fa-user"></i> <?php echo htmlspecialchars($display_user); ?></div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="main-container">
        <h2 style="margin: 0;">PC Units Management</h2>
        <p style="color: #8aa0c5; font-size: 14px; margin-bottom: 30px;">Monitor and manage active workstation sessions</p>
        
        <div class="pc-grid">
            <?php
            $result = $conn->query("SELECT * FROM pcs ORDER BY name ASC");
            if($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()){
                    $status = $row['status'];
                    $cardClass = ($status == 'active') ? 'pc-card active-pc' : 'pc-card';
            ?>
            <div class="<?php echo $cardClass; ?>" onclick="handlePC('<?php echo $row['id']; ?>', '<?php echo $status; ?>', '<?php echo $row['name']; ?>')">
                <h3 style="margin: 0 0 10px 0;"><i class="fas fa-desktop"></i> <?php echo $row['name']; ?></h3>
                <span style="color: <?php echo $status == 'available' ? '#2ecc71' : '#ff4d4d'; ?>; font-size: 12px; font-weight: bold;">
                    ● <?php echo strtoupper($status); ?>
                </span>
                <?php if($status == 'active'): ?>
                    <?php 
                    $s_res = $conn->query("SELECT start_time, time_limit FROM sessions WHERE pc_id='".$row['id']."' AND end_time IS NULL ORDER BY id DESC LIMIT 1");
                    if($s_row = $s_res->fetch_assoc()): 
                    ?>
                        <div class="timer-display">
                            <span class="active-timer" 
                                  data-start="<?php echo $s_row['start_time']; ?>"
                                  data-limit="<?php echo $s_row['time_limit']; ?>"
                                  data-id="<?php echo $row['id']; ?>">00:00:00</span>
                        </div>
                        <div style="font-size: 11px; color: #2ecc71; margin-top: 4px;">
                            ₱<span class="cost-display">0.00</span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <p style="color: #8aa0c5; font-size: 11px; margin-top: 15px;">
                    <?php echo $status == 'available' ? 'Click to start' : 'Click to end session'; ?>
                </p>
            </div>
            <?php } } ?>
        </div>
    </div>

    <div id="sessionModal" class="custom-modal" style="display:none;">
        <div style="background:#0a0e14; border:1px solid #1e293b; padding:30px; border-radius:15px; width:400px; text-align:center;">
            <h2 id="modalTitle" style="color:#38bdf8; margin-top:0;">Start Session</h2>
            <p style="color:#94a3b8;">Select a time package or choose Open Time.</p>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:20px;">
                <button class="modal-btn-open" onclick="startSessionAction('open')">OPEN TIME</button>
                <button class="modal-btn" onclick="startSessionAction('60')">1 HR (₱<?php echo number_format($rates['hourly_rate'], 0); ?>)</button>
                <button class="modal-btn" onclick="startSessionAction('180')">3 HRS (₱<?php echo number_format($rates['rate_3hr'], 0); ?>)</button>
                <button class="modal-btn" onclick="startSessionAction('300')">5 HRS (₱<?php echo number_format($rates['rate_5hr'], 0); ?>)</button>
                <button class="modal-btn" onclick="startSessionAction('420')">7 HRS (₱<?php echo number_format($rates['rate_7hr'], 0); ?>)</button>
                <button class="modal-btn" onclick="startSessionAction('720')" style="grid-column: span 2; border-color:#2ecc71; color:#2ecc71;">12 HOURS (₱<?php echo number_format($rates['rate_12hr'], 0); ?>)</button>
            </div>
            <button onclick="closeModal()" style="margin-top:20px; background:transparent; color:#94a3b8; border:none; cursor:pointer; text-decoration:underline;">Cancel</button>
        </div>
    </div>

    <div id="endSessionModal" class="custom-modal" style="display:none;">
        <div class="custom-modal-content">
            <div class="modal-icon-red"><i class="fas fa-exclamation-triangle"></i></div>
            <h2>End Session?</h2>
            <p>This action will generate the final bill for <span id="modalPcName" style="color:white; font-weight:bold;"></span> and make the unit available.</p>
            <div class="modal-footer-btns">
                <button class="btn-cancel" onclick="closeEndModal()">Cancel</button>
                <button class="btn-confirm-red" id="confirmEndBtn">Yes, End it</button>
            </div>
        </div>
    </div>

    <div id="voidModal" class="custom-modal" style="display:none;">
        <div class="custom-modal-content">
            <div class="modal-icon-red"><i class="fas fa-exclamation-triangle"></i></div>
            <h2 id="voidTitle">Void Transaction?</h2>
            <p>This action will remove the record and deduct it from your daily revenue.</p>
            <div class="modal-footer-btns">
                <button class="btn-cancel" onclick="closeVoidModal()">Cancel</button>
                <button class="btn-confirm-red" id="confirmVoidBtn">Yes, Void it</button>
            </div>
        </div>
    </div>

<script>
    // System logic for timers and modal handling
    const dbRates = {
        hr1: <?php echo $rates['hourly_rate']; ?>,
        hr3: <?php echo $rates['rate_3hr']; ?>,
        hr5: <?php echo $rates['rate_5hr']; ?>,
        hr7: <?php echo $rates['rate_7hr']; ?>,
        hr12: <?php echo $rates['rate_12hr']; ?>,
        min: <?php echo $rates['minimum_charge']; ?>
    };

    function handlePC(id, status, name) {
        if (status === 'available') {
            currentPCId = id;
            document.getElementById('modalTitle').innerText = "Start " + name;
            document.getElementById('sessionModal').style.display = 'flex';
        } else {
            document.getElementById('endSessionModal').style.display = 'flex';
            document.getElementById('modalPcName').innerText = name;
            document.getElementById('confirmEndBtn').onclick = function() {
                window.location.href = "end_session.php?id=" + id;
            };
        }
    }

    function closeModal() { document.getElementById('sessionModal').style.display = 'none'; }
    function closeEndModal() { document.getElementById('endSessionModal').style.display = 'none'; }
    function closeVoidModal() { document.getElementById('voidModal').style.display = 'none'; }

    function startSessionAction(choice) {
        let url = "start_session.php?id=" + currentPCId;
        if (choice !== 'open') url += "&mins=" + choice;
        window.location.href = url;
    }

    function updateTimers() {
        const timers = document.querySelectorAll('.active-timer');
        timers.forEach(timer => {
            const startStr = timer.getAttribute('data-start').replace(/-/g, "/"); 
            const limit = timer.getAttribute('data-limit');
            const startTime = new Date(startStr).getTime();
            const now = new Date().getTime();
            const diff = now - startTime;

            if (diff >= 0) {
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                timer.innerText = `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;

                const costSpan = timer.closest('.pc-card').querySelector('.cost-display');
                if (costSpan) {
                    let displayPrice = 0;
                    if (limit == 60) displayPrice = dbRates.hr1;
                    else if (limit == 180) displayPrice = dbRates.hr3;
                    else if (limit == 300) displayPrice = dbRates.hr5;
                    else if (limit == 420) displayPrice = dbRates.hr7;
                    else if (limit == 720) displayPrice = dbRates.hr12;
                    else displayPrice = Math.max(dbRates.min, (diff / 3600000) * dbRates.hr1);
                    costSpan.innerText = parseFloat(displayPrice).toFixed(2);
                }
            }
        });
    }

    function updateClock() {
        const now = new Date();
        document.getElementById('systemClock').innerText = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
        });
    }

    setInterval(updateTimers, 1000);
    setInterval(updateClock, 1000);

    window.onclick = function(event) {
        if (event.target == document.getElementById('sessionModal')) closeModal();
        if (event.target == document.getElementById('endSessionModal')) closeEndModal();
        if (event.target == document.getElementById('voidModal')) closeVoidModal();
    }
</script>
</body>
</html>