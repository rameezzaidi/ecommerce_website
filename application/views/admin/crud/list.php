<?php $cols = $crud->cfg('columns'); $slug = $crud->cfg('slug'); $filter = $crud->cfg('filter'); $canAdd = $crud->cfg('can_add'); $canEdit = $crud->cfg('can_edit'); $canDel = $crud->cfg('can_delete'); ?>
<div class="cardx">
  <div class="hd">
    <form class="d-flex gap-2 flex-wrap" method="get" action="<?= site_url('admin/' . $slug) ?>">
      <input class="fi" style="width:230px" name="q" value="<?= e($q) ?>" placeholder="Search...">
      <?php if ($filter): ?><select class="fi" name="<?= $filter['field'] ?>" style="width:190px" onchange="this.form.submit()"><option value="">All <?= e($filter['label']) ?></option><?php foreach ($filter['options'] as $k => $v): ?><option value="<?= e($k) ?>" <?= (string)$flt === (string)$k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select><?php endif; ?>
      <button class="btn-a2"><?= icon('search', 15) ?> Search</button>
      <?php if ($q !== '' || ($filter && $flt !== '')): ?><a class="btn-a2" href="<?= site_url('admin/' . $slug) ?>">Clear</a><?php endif; ?>
    </form>
    <div class="d-flex gap-2 align-items-center"><span style="color:var(--mute);font-size:13px"><?= (int)$total ?> record<?= $total == 1 ? '' : 's' ?></span>
      <?php if ($slug === 'subscribers'): ?><a class="btn-a2" href="<?= site_url('admin/subscribers/export') ?>"><?= icon('download', 15) ?> Export CSV</a><?php endif; ?>
      <?php if ($canAdd): ?><a class="btn-a" href="<?= site_url('admin/' . $slug . '/add') ?>"><?= icon('plus', 16) ?> Add <?= e($crud->cfg('singular')) ?></a><?php endif; ?></div>
  </div>
  <div class="table-responsive"><table class="tb">
    <thead><tr><?php foreach ($cols as $k => $c): ?><th><?= e($c['label']) ?></th><?php endforeach; ?><th style="text-align:right">Actions</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="<?= count($cols) + 1 ?>" style="text-align:center;padding:50px;color:var(--mute)">No records found.</td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?><tr>
      <?php foreach ($cols as $k => $c): $v = isset($r->$k) ? $r->$k : ''; ?><td>
        <?php switch ($c['type']):
          case 'image': ?><img class="thumb" src="<?= upload_url($v) ?>" alt=""><?php break;
          case 'strong': ?><b><?= e($v) ?></b><?php break;
          case 'muted': ?><span style="color:var(--mute)"><?= e($v) ?></span><?php break;
          case 'badge': ?><span class="badge-st st-muted"><?= e(ucfirst($v)) ?></span><?php break;
          case 'money': ?><?= $v !== null && $v !== '' && (float)$v > 0 ? money($v) : '<span style="color:#bbb">-</span>' ?><?php break;
          case 'date': ?><?= $v ? date('d M Y', strtotime($v)) : '' ?><?php break;
          case 'trunc': ?><span style="color:var(--mute)"><?= e(mb_strimwidth((string)$v, 0, 70, '...')) ?></span><?php break;
          case 'stars': ?><?= stars($v, 13) ?><?php break;
          case 'color': ?><span style="display:inline-block;width:26px;height:26px;border-radius:50%;background:<?= e($v) ?>;border:1px solid #ccc"></span><?php break;
          case 'stock': ?><?= (int)$v <= 0 ? '<span class="badge-st st-bad">Sold out</span>' : ((int)$v <= 5 ? '<span class="badge-st st-warn">' . (int)$v . ' low</span>' : (int)$v) ?><?php break;
          case 'switch': ?><label class="sw" data-url="<?= site_url('admin/' . $slug . '/toggle/' . $r->id . '/' . $k) ?>"><input type="checkbox" <?= $v ? 'checked' : '' ?>><i></i></label><?php break;
          default: ?><?= e($v) ?><?php endswitch; ?>
      </td><?php endforeach; ?>
      <td style="text-align:right;white-space:nowrap">
        <?php if ($canEdit): ?><a class="btn-a2 btn-sm2" href="<?= site_url('admin/' . $slug . '/edit/' . $r->id) ?>"><?= icon('edit-2', 13) ?> Edit</a><?php endif; ?>
        <?php if ($canDel): ?><form class="d-inline" method="post" action="<?= site_url('admin/' . $slug . '/delete/' . $r->id) ?>" data-confirm="Delete this record permanently?"><?= csrf_field() ?><button class="btn-a2 btn-sm2 btn-danger2" aria-label="Delete"><?= icon('trash-2', 13) ?></button></form><?php endif; ?>
      </td></tr><?php endforeach; ?>
    </tbody></table></div>
  <?php if ($pages > 1): ?><div class="bd d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-top:1px solid var(--line);padding:14px 22px"><span style="color:var(--mute);font-size:13px">Page <?= $page ?> of <?= $pages ?></span>
    <div class="pager"><?php $qs = $_GET; for ($n = 1; $n <= $pages; $n++): $qs['page'] = $n; ?><?= $n == $page ? '<span class="cur">' . $n . '</span>' : '<a href="?' . http_build_query($qs) . '">' . $n . '</a>' ?><?php endfor; ?></div></div><?php endif; ?>
</div>
