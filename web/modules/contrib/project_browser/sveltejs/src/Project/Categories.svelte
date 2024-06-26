<script>
  // eslint-disable-next-line import/no-mutable-exports,import/prefer-default-export
  export let moduleCategories;
  export let toggleView;

  const extraCategories = moduleCategories.splice(3);
  if (extraCategories.length) {
    const { Drupal } = window;
    const overflowText = Drupal.t('+ @count more', {
      '@count': extraCategories.length,
    });
    moduleCategories.push({ id: 'overflow', name: overflowText });
  }
</script>

<div class="pb-project-categories" data-label="Categories">
  {#if typeof moduleCategories !== 'undefined' && moduleCategories.length}
    <ul
      class:pb-project-categories__list--centered={toggleView === 'Grid'}
      class="pb-project-categories__list"
    >
      {#each moduleCategories || [] as category}
        <li
          class="pb-project-categories__item"
          class:pb-project-categories__item--extra={category.id === 'overflow'}
        >
          {category.name}
        </li>
      {/each}
    </ul>
  {/if}
</div>
