<script>
  import { filters } from '../stores';

  export let filterTitle;
  export let filterData;
  export let changeHandler;
  export let filterType;
</script>

<fieldset class="filter-group">
  <legend class="filter-group__title-wrapper">
    {filterTitle}:
  </legend>
  <div class="filter-group__filter-options-wrapper">
    <div class="filter-group__filter-options">
      {#each Object.entries(filterData) as [id, label]}
        <div class="filter-group__filter-option">
          <input
            type="radio"
            name={filterType}
            id={filterType + id}
            class="filter-group__radio form-radio form-boolean form-boolean--type-radio"
            bind:group={$filters[filterType]}
            on:change={changeHandler}
            value={id}
          />
          <slot class="filter-group__label-slot" name="label" {id} {label}>
            <label class="filter-group__option-label" for={filterType + id}>
              {label}
            </label>
          </slot>
        </div>
      {/each}
    </div>
  </div>
</fieldset>
