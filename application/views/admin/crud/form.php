<?php $fields = $crud->cfg('fields'); $slug = $crud->cfg('slug'); $val = function ($k, $f) use ($row, $old) { if (isset($old[$k])) return $old[$k]; if ($row && isset($row->$k)) return $row->$k; return isset($f['default']) ? $f['default'] : ''; }; ?>
<form method="post" action="<?= site_url('admin/' . $slug . '/save' . ($id ? '/' . $id : '')) ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <?php if (!empty($errors)): ?><div class="alert-d alert-error"><?= icon('alert-circle', 18) ?><div><?= $errors ?></div></div><?php endif; ?>
  <div class="cardx"><div class="hd"><h5><?= $id ? 'Edit' : 'New' ?> <?= e($crud->cfg('singular')) ?></h5><a href="<?= site_url('admin/' . $slug) ?>" class="btn-a2 btn-sm2"><?= icon('arrow-left', 14) ?> Back</a></div>
  <div class="bd"><div class="row g-3">
    <?php foreach ($fields as $k => $f): $v = $val($k, $f); $col = isset($f['col']) ? (int)$f['col'] : 12; ?>
    <div class="col-md-<?= $col ?>">
      <?php if ($f['type'] === 'switch'): ?>
        <div class="d-flex align-items-center gap-2 mt-md-4"><label class="sw"><input type="checkbox" name="<?= $k ?>" value="1" <?= (!empty($v) && $v !== '0') ? 'checked' : '' ?>><i></i></label><span style="font-weight:500"><?= e($f['label']) ?></span></div>
      <?php else: ?>
        <label class="fl" for="f_<?= $k ?>"><?= e($f['label']) ?><?= !empty($f['required']) ? ' *' : '' ?></label>
        <?php switch ($f['type']):
          case 'textarea': case 'html': ?><textarea class="fi" id="f_<?= $k ?>" name="<?= $k ?>" rows="<?= isset($f['rows']) ? $f['rows'] : 4 ?>"><?= e($v) ?></textarea><?php break;
          case 'select': ?><select class="fi" id="f_<?= $k ?>" name="<?= $k ?>"><?php foreach ($f['options'] as $ok => $ol): ?><option value="<?= e($ok) ?>" <?= (string)$v === (string)$ok ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?></select><?php break;
          case 'image': ?><div class="d-flex gap-3 align-items-start"><img class="img-prev" id="prev_<?= $k ?>" src="<?= upload_url($v) ?>" alt="" style="<?= $v ? '' : 'display:none' ?>"><div style="flex:1"><input class="fi" type="file" id="f_<?= $k ?>" name="<?= $k ?>" accept="image/*" data-prev="#prev_<?= $k ?>"><?php if ($v): ?><label style="font-size:12.5px;margin-top:8px;display:block"><input type="checkbox" name="remove_<?= $k ?>" value="1"> Remove current image</label><?php endif; ?></div></div><?php break;
          case 'color': ?><div class="d-flex align-items-center gap-2"><input type="color" id="f_<?= $k ?>" name="<?= $k ?>" value="<?= e($v ?: '#111111') ?>" style="width:54px;height:40px;border:1px solid var(--line);border-radius:6px;padding:2px;background:#fff"><span class="hexv" style="font-family:monospace"><?= e($v ?: '#111111') ?></span></div><?php break;
          case 'password': ?><input class="fi" type="password" id="f_<?= $k ?>" name="<?= $k ?>" autocomplete="new-password"><?php break;
          default: ?><input class="fi" type="<?= in_array($f['type'], array('number', 'date', 'email')) ? $f['type'] : 'text' ?>" id="f_<?= $k ?>" name="<?= $k ?>" value="<?= e($v) ?>" <?= $f['type'] === 'number' ? 'step="any"' : '' ?>><?php endswitch; ?>
        <?php if (!empty($f['help'])): ?><div class="hint"><?= e($f['help']) ?></div><?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($has_gallery)): ?>
    <div class="col-12"><label class="fl">Gallery images</label>
      <?php foreach ($gallery as $g): ?><div class="gal-it"><img class="img-prev" src="<?= upload_url($g->image) ?>" alt=""><label><input type="checkbox" name="remove_gallery[]" value="<?= $g->id ?>"> remove</label></div><?php endforeach; ?>
      <input class="fi mt-2" type="file" name="gallery[]" accept="image/*" multiple><div class="hint">Select several images at once. JPG, PNG, WEBP or GIF up to 4 MB each.</div></div>
    <?php endif; ?>
  </div></div>
  <div class="bd" style="border-top:1px solid var(--line);display:flex;gap:10px"><button class="btn-a"><?= icon('check', 16) ?> Save</button><a href="<?= site_url('admin/' . $slug) ?>" class="btn-a2">Cancel</a></div></div>
</form>
