<div class="row g-4 justify-content-center my-4">
    <div class="col-12 text-center no-print mb-3">
        <h4 class="fw-bold text-dark">Workstations Asset Labels Print Preview</h4>
        <p class="text-muted">Press Ctrl+P / Cmd+P to print these sticker labels. Cut along the borders to stick on physical machines.</p>
        <button onclick="window.print();" class="btn btn-primary px-4"><i class="fa-solid fa-print me-1"></i> Print Sticker Sheets</button>
    </div>

    <!-- Labels Sheet Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        <?php foreach ($computers as $c): ?>
            <div class="col">
                <div class="card p-3" style="border: 2px dashed #333; border-radius: 8px; color: #000; background-color: #fff; box-shadow: none;">
                    <div class="row align-items-center">
                        <!-- Left: Asset QR Code -->
                        <div class="col-4 text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($c['code']) ?>" alt="QR Code" style="width: 85px; height: 85px;">
                        </div>
                        <!-- Right: Specs and Barcode -->
                        <div class="col-8">
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem; color: #111;"><?= esc($c['code']) ?></h6>
                            <div class="small text-muted" style="font-size: 0.75rem;"><?= esc($c['brand']) ?> - <?= esc($c['model']) ?></div>
                            <div class="small text-dark" style="font-size: 0.75rem; line-height: 1.2; margin-top: 4px;">
                                <strong>CPU:</strong> <?= esc($c['cpu']) ?><br>
                                <strong>IP:</strong> <?= esc($c['ip_address']) ?>
                            </div>
                            
                            <!-- Barcode Container -->
                            <div class="mt-2 text-start">
                                <svg class="barcode" 
                                     data-value="<?= esc($c['asset_number']) ?>"
                                     style="height: 35px; max-width: 140px;"></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Load JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize barcodes on load
        var barcodes = document.querySelectorAll('.barcode');
        barcodes.forEach(function(el) {
            var val = el.getAttribute('data-value');
            JsBarcode(el, val, {
                format: "CODE128",
                width: 1.5,
                height: 30,
                displayValue: true,
                fontSize: 10,
                margin: 0
            });
        });
    });
</script>
