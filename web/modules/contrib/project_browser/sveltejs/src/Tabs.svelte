<script>
  import { createEventDispatcher } from 'svelte';
  import { activeTab } from './stores';

  const { Drupal } = window;
  const dispatch = createEventDispatcher();

  // eslint-disable-next-line import/no-mutable-exports,import/prefer-default-export
  export let dataArray = [];
  let tabButtons;
  // Enable arrow navigation between tabs in the tab list
  function onKeydown(e) {
    // Enable arrow navigation between tabs in the tab list
    let tabFocus;

    const tabs = tabButtons.querySelectorAll('[role="tab"]');
    for (let i = 0; i < tabs.length; i++) {
      if (tabs[i].getAttribute('tabindex') === '0') {
        tabFocus = i;
      }
    }

    // Move right
    if (e.keyCode === 39 || e.keyCode === 37) {
      tabs[tabFocus].setAttribute('tabindex', -1);
      if (e.keyCode === 39) {
        tabFocus += 1;
        // If we're at the end, go to the start
        if (tabFocus >= tabs.length) {
          tabFocus = 0;
        }
        // Move left
      } else if (e.keyCode === 37) {
        tabFocus -= 1;
        // If we're at the start, move to the end
        if (tabFocus < 0) {
          tabFocus = tabs.length - 1;
        }
      }
      tabs[tabFocus].setAttribute('tabindex', 0);
      tabs[tabFocus].focus();
    }
  }
</script>

<!--Show tabs only if there are 2 or more plugins enabled.-->
{#if dataArray.length >= 2}
  <nav class="tabs-wrapper tabs-wrapper--secondary is-horizontal">
    <div
      on:keydown={onKeydown}
      role="tablist"
      id="plugin-tabs"
      aria-label={Drupal.t('Plugin tabs')}
      bind:this={tabButtons}
      class="tabs tabs--secondary pb-tabs"
    >
      {#each dataArray.map( (item) => ({ ...item, isActive: item.pluginId === $activeTab }), ) as { pluginId, pluginLabel, totalResults, isActive }}
        <span
          class="tabs__tab pb-tabs__tab"
          class:is-active={isActive === true}
          class:pb-tabs__tab--active={isActive === true}
        >
          <button
            type="button"
            role="tab"
            aria-selected={isActive ? 'true' : 'false'}
            aria-controls={pluginId}
            tabindex={isActive ? '0' : '-1'}
            id={pluginId}
            class="pb-tabs__link tabs__link"
            class:is-active={isActive === true}
            class:pb-tabs__link--active={isActive === true}
            value={pluginId}
            on:click={(event) => {
              dispatch('tabChange', {
                pluginId,
                event,
              });
            }}
          >
            {pluginLabel}
            <br />
            {Drupal.formatPlural(totalResults, '1 result', '@count results')}
            {#if isActive}
              <span class="visually-hidden">({Drupal.t('active tab')})</span>
            {/if}
          </button>
        </span>
      {/each}
    </div>
  </nav>
{/if}
