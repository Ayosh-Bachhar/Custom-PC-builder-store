<?php 
require_once 'pc_builder_logic.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>PC Builder</title>
    <style>
        .container { display: flex; gap: 20px; font-family: Arial, sans-serif; }
        .builder-section { flex: 2; border: 1px solid #ccc; padding: 20px; }
        .summary-section { flex: 1; border: 1px solid #ccc; padding: 20px; background: #f9f9f9; height: fit-content;}
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        select { width: 100%; padding: 8px; }
        .warning { color: red; font-size: 0.9em; margin-top: 5px; display: none; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;}
        .totals { font-size: 1.2em; font-weight: bold; margin-top: 20px; }
        button { padding: 10px 15px; margin-top: 10px; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Custom PC Builder</h1>
    <a href="index.php">Back to Home</a>
    
    <p><?php echo $message; ?></p>

    <div class="container">
        <div class="builder-section">
            <h3>Select Your Parts</h3>
            
            <div class="form-group">
                <label>CPU (Required)</label>
                <select id="select_cpu" onchange="updateBuild()">
                    <option value="">-- Choose CPU --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Motherboard (Required)</label>
                <select id="select_mobo" onchange="updateBuild()">
                    <option value="">-- Choose Motherboard --</option>
                </select>
                <div id="warn_socket" class="warning">⚠️ Warning: CPU Socket and Motherboard Socket do not match!</div>
            </div>

            <div class="form-group">
                <label>RAM (Required)</label>
                <select id="select_ram" onchange="updateBuild()">
                    <option value="">-- Choose RAM --</option>
                </select>
                <div id="warn_ram" class="warning">⚠️ Warning: RAM Type and Motherboard RAM Type do not match!</div>
            </div>

            <div class="form-group">
                <label>Storage (Required)</label>
                <select id="select_storage" onchange="updateBuild()">
                    <option value="">-- Choose Storage --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Power Supply (Required)</label>
                <select id="select_psu" onchange="updateBuild()">
                    <option value="">-- Choose Power Supply --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Graphics Card (Optional)</label>
                <select id="select_gpu" onchange="updateBuild()">
                    <option value="">-- Choose Graphics Card --</option>
                </select>
            </div>
        </div>

        <div class="summary-section">
            <h3>Build Summary</h3>
            <div id="summary_list"></div>

            <div class="totals">
                <p>Estimated Wattage: <span id="total_wattage" style="color: blue;">0</span> W</p>
                <p>Total Price: <span id="total_price" style="color: green;">0.00</span> ৳</p>
            </div>

            <button onclick="exportToText()">Export Build to .txt</button>

            <form method="POST" action="" onsubmit="return validateRequired()">
                <input type="hidden" name="cpu_id" id="form_cpu">
                <input type="hidden" name="mobo_id" id="form_mobo">
                <input type="hidden" name="ram_id" id="form_ram">
                <input type="hidden" name="storage_id" id="form_storage">
                <input type="hidden" name="psu_id" id="form_psu">
                <input type="hidden" name="gpu_id" id="form_gpu">
                <button type="submit" name="add_build_to_cart" style="background: green; color: white; width: 100%;">Add Build to Cart</button>
            </form>
        </div>
    </div>

    <script>
        const productsData = <?php echo $productsJson; ?>;
    </script>
    
    <script src="pc_builder_script.js"></script>

</body>
</html>
