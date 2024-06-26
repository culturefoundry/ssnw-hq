<script>
  import { FULL_MODULE_PATH } from '../constants';

  const { Drupal } = window;

  // eslint-disable-next-line import/prefer-default-export
  export let isOpen;

  /* When the user clicks on the button,
 toggle between hiding and showing the dropdown content */
  function openDropdown() {
    isOpen = !isOpen;
  }
</script>

<div class="search__filter__toggle-container">
  <section aria-label={Drupal.t('Filter settings')}>
    <button
      type="button"
      class="search__filter__toggle form-element"
      class:is_open={isOpen}
      aria-controls="filter-dropdown"
      aria-label={isOpen ? Drupal.t('Close Filter') : Drupal.t('Open Filter')}
      aria-expanded={isOpen.toString()}
      on:click={() => openDropdown()}
      ><img
        src="{FULL_MODULE_PATH}/images/advanced-filter-icon.svg"
        alt="advanced filter icon"
        class="search__filter__toggle-img"
      />Filters
    </button>
  </section>
</div>
