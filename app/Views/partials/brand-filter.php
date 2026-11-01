<form action="<?= $e($base) ?>/shop" method="get" class="brand-filter">
  <input type="hidden" name="category" value="<?= (int) $selectedCategory ?>">
  <input type="hidden" name="q" value="<?= $e($search) ?>">
  <label for="brand-filter">Brand </label>
  <select id="brand-filter" name="brand">
    <option value="0">All brands</option>
    <?php foreach ($brands as $brand): ?>
      <option value="<?= (int) $brand['id'] ?>" <?= $selectedBrand === (int) $brand['id'] ? 'selected' : '' ?>><?= $e($brand['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Filter</button>
</form>