  </div>
</div>
<script src="<?= asset('vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('vendor/chart/chart.umd.js') ?>"></script>
<script src="<?= asset('js/outfit.js') ?>"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
<?php if (!empty($page_js)) foreach ($page_js as $js): ?><script src="<?= asset('js/' . $js) ?>"></script><?php endforeach; ?>
</body></html>
