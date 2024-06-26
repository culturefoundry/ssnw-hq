<script>
  import { onMount } from 'svelte';
  import ActionButton from './Project/ActionButton.svelte';
  import Image from './Project/Image.svelte';
  import ImageCarousel from './ImageCarousel.svelte';
  import { ORIGIN_URL } from './constants';
  import { moduleCategoryFilter, page } from './stores';
  import ProjectIcon from './Project/ProjectIcon.svelte';
  import { numberFormatter } from './util';

  // eslint-disable-next-line import/no-mutable-exports,import/prefer-default-export
  export let project;
  const { Drupal } = window;

  function filterByCategory(id) {
    $moduleCategoryFilter = [id];
    $page = 0;
    window.location.href = `${ORIGIN_URL}/admin/modules/browse`;
  }

  onMount(() => {
    const anchors = document
      .getElementById('description-wrapper')
      .getElementsByTagName('a');
    for (let i = 0; i < anchors.length; i++) {
      anchors[i].setAttribute('target', '_blank');
    }
  });
</script>

<a class="action-link" href="{ORIGIN_URL}/admin/modules/browse">
  <span aria-hidden="true">&#9001&#xA0</span>
  {Drupal.t('Back to Browsing')}
</a>

<div class="pb-module-page">
  <div class="pb-module-page__sidebar">
    <Image sources={project.logo} class="pb-module-page__project-logo" />
    <div class="pb-module-page__actions">
      <ActionButton {project} />
    </div>
    <hr />
    <div class="pb-module-page__details">
      <h4 class="pb-module-page__details-title">{Drupal.t('Details')}</h4>
      {#if project.module_categories.length}
        <p class="pb-module-page__categories-label" id="categories">
          {Drupal.t('Categories:')}
        </p>
        <ul
          class="pb-module-page__categories-list"
          aria-labelledby="categories"
        >
          {#each project.module_categories || [] as category}
            <li
              on:click={() => filterByCategory(category.id)}
              class="pb-module-page__categories-list-item"
            >
              {category.name}
            </li>
          {/each}
        </ul>
      {/if}
      <div class="pb-module-page__module-details">
        {#if project.is_compatible}
          <ProjectIcon
            type="compatible"
            variant="module-details"
            classes="pb-module-page__module-details-icon"
          />
          <p class="pb-module-page__module-details-info">
            {Drupal.t('Compatible with your Drupal installation')}
          </p>
        {/if}
        {#if project.project_usage_total !== -1}
          <ProjectIcon
            type="usage"
            variant="module-details"
            classes="pb-module-page__module-details-icon"
          />
          <p class="pb-module-page__module-details-info">
            {numberFormatter.format(project.project_usage_total)}{Drupal.t(
              ' sites report using this module',
            )}
          </p>
        {/if}
        {#if project.is_covered}
          <ProjectIcon
            type="status"
            variant="module-details"
            classes="pb-module-page__module-details-icon"
          />
          <p class="pb-module-page__module-details-info">
            {Drupal.t(
              'Stable releases for this project are covered by the security advisory policy',
            )}
          </p>
        {/if}
      </div>
    </div>
  </div>
  <div class="pb-module-page__main">
    <h2 class="pb-module-page__title">{project.title}</h2>
    <p class="pb-module-page__author">
      {Drupal.t('By ')}{project.author.name}
    </p>
    {#if project.project_images.length}
      <div class="pb-module-page__carousel-wrapper">
        <ImageCarousel sources={project.project_images} />
      </div>
    {/if}
    <div class="pb-module-page__description" id="description-wrapper">
      {@html project.body.value}
    </div>
  </div>
</div>
